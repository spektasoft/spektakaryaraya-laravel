<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckSiteIntegrityJob;
use App\Jobs\CheckSiteUptimeJob;
use App\Models\MonitoredSite;
use App\Models\User;
use App\Notifications\MonitoringAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MonitoringJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_uptime_job_logs_success_correctly(): void
    {
        $site = MonitoredSite::factory()->create([
            'url' => 'https://example-uptime.com',
            'uptime_status' => 'unknown',
        ]);

        Http::fake([
            'example-uptime.com' => Http::response('', 200),
        ]);

        CheckSiteUptimeJob::dispatchSync($site);

        $site->refresh();
        $this->assertEquals('up', $site->uptime_status);
        $this->assertEquals(200, $site->last_uptime_code);
        $this->assertDatabaseHas('monitored_site_logs', [
            'monitored_site_id' => $site->id,
            'type' => 'uptime',
            'status' => 'up',
            'status_code' => 200,
        ]);
    }

    public function test_uptime_job_detects_down_and_records_alerts(): void
    {
        Notification::fake();

        $adminEmail = 'admin@example.com';
        config(['auth.super_users' => [$adminEmail]]);
        $admin = User::factory()->create(['email' => $adminEmail]);

        $site = MonitoredSite::factory()->create([
            'url' => 'https://example-uptime-down.com',
            'uptime_status' => 'up',
            'name' => 'Test Site',
        ]);

        Http::fake([
            'example-uptime-down.com' => Http::response('', 503),
        ]);

        CheckSiteUptimeJob::dispatchSync($site);

        $site->refresh();
        $this->assertEquals('down', $site->uptime_status);
        $this->assertEquals(503, $site->last_uptime_code);

        Notification::assertSentTo(
            $admin,
            MonitoringAlert::class,
            fn (MonitoringAlert $notification) => $notification->toArray($admin)['title'] === __('monitoring.uptime.alert_title')
        );
    }

    public function test_integrity_job_records_baseline_on_first_scan(): void
    {
        $site = MonitoredSite::factory()->create([
            'url' => 'https://example-integrity-baseline.com',
            'expected_md5_hash' => null,
        ]);

        $html = '<html><head><title>Secure Page</title></head><body><a href="/login">Link</a></body></html>';
        Http::fake([
            'example-integrity-baseline.com' => Http::response($html, 200),
        ]);

        CheckSiteIntegrityJob::dispatchSync($site);

        $site->refresh();
        $this->assertEquals('clean', $site->integrity_status);
        $this->assertEquals(md5($html), $site->expected_md5_hash);
        $this->assertEquals(1, $site->expected_links_count);
        $this->assertEquals(0, $site->expected_scripts_count);
    }

    public function test_integrity_job_flags_altered_content_and_links_spike(): void
    {
        Notification::fake();
        $adminEmail = 'admin@example.com';
        config(['auth.super_users' => [$adminEmail]]);
        $admin = User::factory()->create(['email' => $adminEmail]);

        $site = MonitoredSite::factory()->create([
            'url' => 'https://example-integrity-altered.com',
            'expected_md5_hash' => 'pre_computed_md5_hash',
            'expected_links_count' => 1,
            'expected_scripts_count' => 0,
            'integrity_status' => 'clean',
        ]);

        $compromisedHtml = '<html><head><title>De-Faced</title></head><body><a href="scam.com">1</a><a href="scam2.com">2</a><a href="scam3.com">3</a><a href="scam4.com">4</a><script src="bad.js"></script></body></html>';
        Http::fake([
            'example-integrity-altered.com' => Http::response($compromisedHtml, 200),
        ]);

        CheckSiteIntegrityJob::dispatchSync($site);

        $site->refresh();
        $this->assertEquals('compromised', $site->integrity_status);

        Notification::assertSentTo(
            $admin,
            MonitoringAlert::class
        );
    }
}
