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
                $this->site->recalibrateBaseline($content);
                $this->site->update(['integrity_status' => 'clean']);

                return;
            }

            $isHashMismatch = $currentHash !== $this->site->expected_md5_hash;

            $linkViolation = false;
            if ($this->site->expected_links_count > 0) {
                $increaseRatio = $currentLinks / $this->site->expected_links_count;
                if ($increaseRatio > 1.5) {
                    $linkViolation = true;
                }
            } elseif ($currentLinks > 5) {
                $linkViolation = true;
            }

            $scriptViolation = false;
            if ($this->site->expected_scripts_count > 0) {
                $increaseRatio = $currentScripts / $this->site->expected_scripts_count;
                if ($increaseRatio > 1.5) {
                    $scriptViolation = true;
                }
            } elseif ($currentScripts > 5) {
                $scriptViolation = true;
            }

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
                $this->notifyAdmins(
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

    protected function notifyAdmins(string $title, string $message): void
    {
        $superUsers = config('auth.super_users', []);

        if (empty($superUsers)) {
            return;
        }

        $admins = User::whereIn('email', $superUsers)->get();

        Notification::send($admins, new MonitoringAlert($title, $message));
    }
}
