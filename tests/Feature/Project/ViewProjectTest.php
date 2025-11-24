<?php

namespace Tests\Feature\Project;

use App\Enums\Project\Status;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_detail_page_loads_correctly(): void
    {
        $project = Project::factory()->create([
            'name' => ['en' => 'Test Project'],
            'status' => Status::Publish,
        ]);

        $response = $this->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertSee('Test Project');
    }
}
