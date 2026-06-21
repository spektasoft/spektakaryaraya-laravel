<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckSiteUptimeJob;
use App\Models\MonitoredSite;
use App\Models\User;
use App\Notifications\MonitoringAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AlertNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_creator_receives_alert_notification(): void
    {
        Notification::fake();

        $creator = User::factory()->create(['email' => 'creator@example.com']);
        $site = MonitoredSite::factory()->create([
            'url' => 'https://example.com',
            'creator_id' => $creator->id,
            'uptime_status' => 'up',
        ]);

        Http::fake([
            'https://example.com' => Http::response('', 500),
        ]);

        $job = new CheckSiteUptimeJob($site);
        $job->handle();

        Notification::assertSentTo(
            $creator,
            MonitoringAlert::class
        );
    }
}
