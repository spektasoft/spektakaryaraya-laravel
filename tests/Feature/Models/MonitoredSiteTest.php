<?php

namespace Tests\Feature\Models;

use App\Models\MonitoredSite;
use App\Models\MonitoredSiteLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoredSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_recalibrate_baseline(): void
    {
        /** @var MonitoredSite $site */
        $site = MonitoredSite::factory()->create();
        $sampleHtml = '<html><head><title>Title</title></head><body><a href="1">Link 1</a><a href="2">Link 2</a><script>console.log("1");</script></body></html>';

        $site->recalibrateBaseline($sampleHtml);

        $site->refresh();
        $this->assertEquals(md5($sampleHtml), $site->expected_md5_hash);
        $this->assertEquals(2, $site->expected_links_count);
        $this->assertEquals(1, $site->expected_scripts_count);
    }

    public function test_active_scope_only_returns_active_sites(): void
    {
        MonitoredSite::factory()->create(['is_active' => true]);
        MonitoredSite::factory()->create(['is_active' => false]);

        $this->assertCount(1, MonitoredSite::active()->get());
    }

    public function test_pruning_removes_old_logs(): void
    {
        /** @var MonitoredSite $site */
        $site = MonitoredSite::factory()->create();

        /** @var MonitoredSiteLog $oldLog */
        $oldLog = MonitoredSiteLog::factory()->create([
            'monitored_site_id' => $site->id,
            'created_at' => now()->subDays(31),
        ]);

        /** @var MonitoredSiteLog $newLog */
        $newLog = MonitoredSiteLog::factory()->create([
            'monitored_site_id' => $site->id,
            'created_at' => now(),
        ]);

        $this->artisan('model:prune', [
            '--model' => [MonitoredSiteLog::class],
        ]);

        $this->assertDatabaseHas('monitored_site_logs', ['id' => $newLog->id]);
        $this->assertDatabaseMissing('monitored_site_logs', ['id' => $oldLog->id]);
    }
}
