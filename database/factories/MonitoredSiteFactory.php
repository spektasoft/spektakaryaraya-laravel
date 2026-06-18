<?php

namespace Database\Factories;

use App\Models\MonitoredSite;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitoredSite>
 */
class MonitoredSiteFactory extends Factory
{
    protected $model = MonitoredSite::class;

    public function definition(): array
    {
        return [
            'creator_id' => User::factory(),
            'project_id' => Project::factory(),
            'name' => ['en' => $this->faker->domainName()],
            'url' => $this->faker->url(),
            'is_active' => true,
            'uptime_status' => 'unknown',
            'integrity_status' => 'unknown',
        ];
    }
}
