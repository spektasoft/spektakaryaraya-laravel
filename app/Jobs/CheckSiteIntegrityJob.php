<?php

namespace App\Jobs;

use App\Models\MonitoredSite;
use App\Models\User;
use App\Notifications\MonitoringAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class CheckSiteIntegrityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public MonitoredSite $site) {}

    public function handle(): void
    {
        $previousStatus = $this->site->integrity_status;

        try {
            $content = $this->site->fetchCurrentContent();
            $normalized = $this->site->normalizeContent($content);

            $currentHash = md5($normalized);
            $currentLinks = preg_match_all('/<a[\s>]/i', $content);
            $currentScripts = preg_match_all('/<script[\s>]/i', $content);

            if (is_null($this->site->expected_md5_hash)) {
                if (strlen(trim($content)) < 100) {
                    throw new \Exception('Failed to capture initial baseline: content length is too short.');
                }
                $this->site->recalibrateBaseline($content);
                $this->site->update(['integrity_status' => 'clean']);

                return;
            }

            $isHashMismatch = $currentHash !== $this->site->expected_md5_hash;

            $linkViolation = $currentLinks > $this->site->expected_links_count;
            $scriptViolation = $currentScripts > $this->site->expected_scripts_count;

            $isCompromised = $isHashMismatch || $linkViolation || $scriptViolation;
            $status = $isCompromised ? 'compromised' : 'clean';

            $details = [
                'current_hash' => $currentHash,
                'expected_hash' => $this->site->expected_md5_hash,
                'current_links' => (int) $currentLinks,
                'expected_links' => (int) $this->site->expected_links_count,
                'current_scripts' => (int) $currentScripts,
                'expected_scripts' => (int) $this->site->expected_scripts_count,
            ];

            $this->site->update([
                'integrity_status' => $status,
                'last_integrity_checked_at' => now(),
                'last_md5_hash' => $currentHash,
                'last_links_count' => $currentLinks,
                'last_scripts_count' => $currentScripts,
                'last_error' => null,
            ]);

            $this->site->logs()->create([
                'type' => 'integrity',
                'status' => $status,
                'details' => $details,
            ]);

            if ($status === 'compromised' && $previousStatus !== 'compromised') {
                $violations = [];
                if ($isHashMismatch) {
                    $violations[] = __('monitoring.integrity.violations.checksum');
                }
                if ($linkViolation) {
                    $violations[] = __('monitoring.integrity.violations.links', ['current' => $currentLinks, 'expected' => $this->site->expected_links_count]);
                }
                if ($scriptViolation) {
                    $violations[] = __('monitoring.integrity.violations.scripts', ['current' => $currentScripts, 'expected' => $this->site->expected_scripts_count]);
                }

                $msg = implode(', ', $violations);
                $this->notifyStakeholders(
                    __('monitoring.integrity.alert_title'),
                    __('monitoring.integrity.alert_body', ['name' => $this->site->name, 'violations' => $msg])
                );
            }

        } catch (\Exception $e) {
            $this->site->update([
                'integrity_status' => 'unknown',
                'last_integrity_checked_at' => now(),
                'last_error' => __('monitoring.integrity.log_error', ['error' => $e->getMessage()]),
            ]);

            $this->site->logs()->create([
                'type' => 'integrity',
                'status' => 'unknown',
                'details' => ['error' => $e->getMessage()],
            ]);
        }
    }

    protected function notifyStakeholders(string $title, string $message): void
    {
        $recipients = collect();

        if ($this->site->creator) {
            $recipients->push($this->site->creator);
        }

        $superUsers = config('auth.super_users', []);
        if (! empty($superUsers)) {
            $admins = User::whereIn('email', $superUsers)->get();
            $recipients = $recipients->merge($admins);
        }

        $recipients = $recipients->unique('id');

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new MonitoringAlert($title, $message));
        }
    }
}
