<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckSiteUptimeJob;
use App\Models\MonitoredSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckSiteUptimeJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_uptime_check_falls_back_to_get_on_method_not_allowed(): void
    {
        $site = MonitoredSite::factory()->create(['url' => 'https://example.com']);

        Http::fake([
            'https://example.com' => function (Request $request) {
                if ($request->method() === 'HEAD') {
                    return Http::response('', 405);
                }

                return Http::response('<html>OK</html>', 200);
            },
        ]);

        $job = new CheckSiteUptimeJob($site);
        $job->handle();

        $site->refresh();
        $this->assertEquals('up', $site->uptime_status);
        $this->assertEquals(200, $site->last_uptime_code);
    }
}
