<?php

namespace Tests\Feature\Filament\Resources;

use App\Enums\MonitoredSite\Status;
use App\Filament\Resources\MonitoredSites\MonitoredSiteResource;
use App\Filament\Resources\MonitoredSites\Pages\CreateMonitoredSite;
use App\Filament\Resources\MonitoredSites\Pages\EditMonitoredSite;
use App\Filament\Resources\MonitoredSites\Pages\ListMonitoredSites;
use App\Models\MonitoredSite;
use App\Models\Permission;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class MonitoredSiteResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);

        $permissions = [
            'view_any_monitored::site',
            'create_monitored::site',
            'update_monitored::site',
            'delete_monitored::site',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $user->givePermissionTo($permission);
        }
    }

    public function test_can_access_list_monitored_sites_page(): void
    {
        $this->get(MonitoredSiteResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_create_monitored_site_via_form(): void
    {
        $project = Project::factory()->create();

        Livewire::test(CreateMonitoredSite::class)
            ->fillForm([
                'name' => ['en' => 'Production Site'],
                'url' => 'https://example.com',
                'project_id' => $project->id,
                'status' => Status::Active,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('monitored_sites', [
            'url' => 'https://example.com',
            'project_id' => $project->id,
        ]);
    }

    public function test_can_update_monitored_site_via_form(): void
    {
        $site = MonitoredSite::factory()->create();

        Livewire::test(EditMonitoredSite::class, ['record' => $site->getRouteKey()])
            ->fillForm([
                'url' => 'https://updated-example.com',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('monitored_sites', [
            'id' => $site->id,
            'url' => 'https://updated-example.com',
        ]);
    }

    public function test_can_recalibrate_monitored_site(): void
    {
        Http::fake([
            'example.com/*' => Http::response('<html>Test Content</html>', 200),
        ]);

        $site = MonitoredSite::factory()->create([
            'url' => 'https://example.com/site',
        ]);

        Livewire::test(ListMonitoredSites::class)
            ->callTableAction('recalibrate', $site)
            ->assertHasNoTableActionErrors();

        Http::assertSent(fn (Request $request) => $request->url() === $site->url);
    }
}
