<?php

namespace Tests\Feature\Home;

use App\Enums\Project\Status;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_with_200_ok(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_project_list_contains_factory_project_name(): void
    {
        $project = Project::factory()->create([
            'name' => ['en' => 'Test Project', 'id' => 'Proyek Tes'],
            'status' => Status::Publish,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Test Project');
    }

    public function test_partner_list_is_sorted_alphabetically(): void
    {
        $partnerC = Partner::factory()->create(['name' => ['en' => 'Charlie']]);
        $partnerA = Partner::factory()->create(['name' => ['en' => 'Alpha']]);
        $partnerB = Partner::factory()->create(['name' => ['en' => 'Beta']]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Alpha', 'Beta', 'Charlie']);
    }
}
