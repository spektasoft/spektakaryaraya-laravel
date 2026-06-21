<?php

namespace Database\Factories;

use App\Models\MonitoredSite;
use App\Models\MonitoredSiteLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitoredSiteLog>
 */
class MonitoredSiteLogFactory extends Factory
{
    protected $model = MonitoredSiteLog::class;

    public function definition(): array
    {
        return [
            'monitored_site_id' => MonitoredSite::factory(),
            'type' => 'uptime',
            'status' => 'up',
            'status_code' => 200,
            'latency' => 110,
            'details' => null,
        ];
    }
}
