<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckSiteIntegrityJob;
use App\Models\MonitoredSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckSiteIntegrityJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_integrity_check_detects_injected_script(): void
    {
        $site = MonitoredSite::factory()->create([
            'url' => 'https://example.com',
            'expected_md5_hash' => md5('<html><body>original</body></html>'),
            'expected_links_count' => 0,
            'expected_scripts_count' => 0,
        ]);

        Http::fake([
            'https://example.com' => Http::response('<html><body>original<script src="malicious.js"></script></body></html>', 200),
        ]);

        $job = new CheckSiteIntegrityJob($site);
        $job->handle();

        $site->refresh();
        $this->assertEquals('compromised', $site->integrity_status);
    }
}
