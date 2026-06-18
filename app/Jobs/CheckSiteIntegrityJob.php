<?php

namespace App\Jobs;

use App\Models\MonitoredSite;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

class CheckSiteIntegrityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public MonitoredSite $site) {}

    public function handle(): void
    {
        $previousStatus = $this->site->integrity_status;

        try {
            $response = Http::withOptions([
                'stream' => true,
                'timeout' => 15,
                'connect_timeout' => 5,
                'verify' => true,
            ])->get($this->site->url);

            if (! $response->successful()) {
                throw new \Exception('Unsuccessful HTTP Status: '.$response->status());
            }

            /** @var StreamInterface $body */
            $body = $response->toPsrResponse()->getBody();

            $content = '';
            // Read in chunks up to 20KB
            while (! $body->eof() && strlen($content) < 20480) {
                $chunk = $body->read(1024);
                $content .= (string) $chunk;
            }
            $body->close();

            $currentHash = md5($content);
            $currentLinks = preg_match_all('/<a[\s>]/i', $content);
            $currentScripts = preg_match_all('/<script[\s>]/i', $content);

            if (is_null($this->site->expected_md5_hash)) {
                $this->site->recalibrateBaseline($content);
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
                    $violations[] = 'Checksum mismatch';
                }
                if ($linkViolation) {
                    $violations[] = "Links count spiked ({$currentLinks} vs expected {$this->site->expected_links_count})";
                }
                if ($scriptViolation) {
                    $violations[] = "Scripts count spiked ({$currentScripts} vs expected {$this->site->expected_scripts_count})";
                }

                $msg = implode(', ', $violations);
                $this->notifyAdmins("Security Alert: {$this->site->name} integrity scan failed. Violations: {$msg}");
            }

        } catch (\Exception $e) {
            $this->site->update([
                'integrity_status' => 'unknown',
                'last_integrity_checked_at' => now(),
                'last_error' => 'Integrity Check Failure: '.$e->getMessage(),
            ]);

            $this->site->logs()->create([
                'type' => 'integrity',
                'status' => 'unknown',
                'details' => ['error' => $e->getMessage()],
            ]);
        }
    }

    protected function notifyAdmins(string $message): void
    {
        $admins = User::all();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('Security System Alert')
                ->body($message)
                ->danger()
                ->sendToDatabase($admin);
        }
    }
}
