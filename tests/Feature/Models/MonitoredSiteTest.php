<?php

namespace Tests\Feature\Models;

use App\Enums\MonitoredSite\Status;
use App\Jobs\CheckSiteIntegrityJob;
use App\Models\MonitoredSite;
use App\Models\MonitoredSiteLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MonitoredSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    public function test_can_recalibrate_baseline(): void
    {
        /** @var MonitoredSite $site */
        $site = MonitoredSite::factory()->create();
        $sampleHtml = '<html><body><h1>Test</h1><a href="/link1">Link 1</a><a href="/link2">Link 2</a><script src="test.js"></script></body></html>';

        $site->recalibrateBaseline($sampleHtml);

        $site->refresh();
        // The model normalizes content (removing whitespace) before hashing
        $normalized = $site->normalizeContent($sampleHtml);
        $this->assertEquals(md5($normalized), $site->expected_md5_hash);
        $this->assertEquals(2, $site->expected_links_count);
        $this->assertEquals(1, $site->expected_scripts_count);
    }

    public function test_active_scope_returns_only_active_sites(): void
    {
        Bus::fake();

        MonitoredSite::factory()->count(3)->create(['status' => Status::Active]);
        MonitoredSite::factory()->count(2)->create(['status' => Status::Disabled]);

        $this->assertEquals(3, MonitoredSite::active()->count());
    }

    public function test_pruning_removes_old_logs(): void
    {
        Bus::fake();

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

    public function test_it_dispatches_integrity_job_on_creation(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $project = Project::factory()->create();

        $site = MonitoredSite::create([
            'creator_id' => $user->id,
            'project_id' => $project->id,
            'name' => ['en' => 'Test Site'],
            'url' => 'https://example.com',
            'status' => Status::Active,
        ]);

        $this->assertEquals('pending', $site->integrity_status);
        Bus::assertDispatched(CheckSiteIntegrityJob::class, function (CheckSiteIntegrityJob $job) use ($site) {
            return $job->site->id === $site->id;
        });
    }
}
