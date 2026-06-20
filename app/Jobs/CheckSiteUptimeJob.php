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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class CheckSiteUptimeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public MonitoredSite $site) {}

    public function handle(): void
    {
        $startTime = microtime(true);
        $previousStatus = $this->site->uptime_status;

        try {
            $response = Http::withOptions([
                'timeout' => 10,
                'connect_timeout' => 5,
                'verify' => true,
            ])->head($this->site->url);

            $latency = (int) ((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();
            $isUp = $response->successful();

            $status = $isUp ? 'up' : 'down';

            $this->site->update([
                'uptime_status' => $status,
                'last_uptime_code' => $statusCode,
                'last_uptime_checked_at' => now(),
                'last_uptime_latency' => $latency,
                'last_error' => $isUp ? null : __('monitoring.uptime.log_error', ['code' => $statusCode]),
            ]);

            $this->site->logs()->create([
                'type' => 'uptime',
                'status' => $status,
                'status_code' => $statusCode,
                'latency' => $latency,
            ]);

            if ($status === 'down' && $previousStatus !== 'down') {
                $this->notifyAdmins(
                    __('monitoring.uptime.alert_title'),
                    __('monitoring.uptime.alert_body', ['name' => $this->site->name, 'code' => $statusCode])
                );
            }
        } catch (\Exception $e) {
            $latency = (int) ((microtime(true) - $startTime) * 1000);

            $this->site->update([
                'uptime_status' => 'down',
                'last_uptime_code' => null,
                'last_uptime_checked_at' => now(),
                'last_uptime_latency' => $latency,
                'last_error' => $e->getMessage(),
            ]);

            $this->site->logs()->create([
                'type' => 'uptime',
                'status' => 'down',
                'status_code' => null,
                'latency' => $latency,
                'details' => ['error' => $e->getMessage()],
            ]);

            if ($previousStatus !== 'down') {
                $this->notifyAdmins(
                    __('monitoring.uptime.alert_title'),
                    __('monitoring.uptime.connection_failed', ['name' => $this->site->name, 'error' => $e->getMessage()])
                );
            }
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
