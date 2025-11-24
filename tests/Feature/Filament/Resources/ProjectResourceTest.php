<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Models\Media;
use App\Models\Partner;
use App\Models\Permission;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create and assign permissions
        $permissions = [
            'view_any_project',
            'create_project',
            'update_project',
            'delete_project',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $user->givePermissionTo($permission);
        }

        config()->set('app.supported_locales', ['en', 'id']);
    }

    public function test_can_create_project(): void
    {
        $media = Media::factory()->create();
        $partner = Partner::factory()->create();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'name' => ['en' => 'Test Project'],
                'start_date' => '2023-01-01',
                'status' => 'publish',
                'logo_id' => $media->id,
                'partners' => [$partner->id],
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'name->en' => 'Test Project',
            'status' => 'publish',
            'logo_id' => $media->id,
        ]);

        $project = Project::where('name->en', 'Test Project')->first();
        $this->assertNotNull($project);
        $this->assertCount(1, $project->partners);
        $this->assertNotNull($project->partners->first());
        $this->assertEquals($partner->id, $project->partners->first()->id);
    }

    public function test_can_update_project(): void
    {
        $media = Media::factory()->create();
        $project = Project::factory()->create([
            'logo_id' => $media->id,
        ]);
        $newPartner = Partner::factory()->create();

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->fillForm([
                'name' => ['en' => 'Updated Project Name'],
                'partners' => [$newPartner->id],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name->en' => 'Updated Project Name',
        ]);

        $project->refresh();
        $this->assertCount(1, $project->partners);
        $this->assertNotNull($project->partners->first());
        $this->assertEquals($newPartner->id, $project->partners->first()->id);
    }
}
