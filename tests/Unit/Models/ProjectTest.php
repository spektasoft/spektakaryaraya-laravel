<?php

namespace Tests\Unit\Models;

use App\Models\Media;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_partners(): void
    {
        $project = Project::factory()->create();
        $partner = Partner::factory()->create();

        $project->partners()->attach($partner);

        $this->assertTrue($project->partners->contains($partner));
        $this->assertInstanceOf(Partner::class, $project->partners->first());
    }

    public function test_it_belongs_to_logo(): void
    {
        $media = Media::factory()->create();
        $project = Project::factory()->create(['logo_id' => $media->id]);

        $this->assertInstanceOf(Media::class, $project->logo);
        $this->assertEquals($media->id, $project->logo->id);
    }

    public function test_it_has_translatable_name(): void
    {
        $project = Project::factory()->create(['name' => ['en' => 'English Name', 'id' => 'Indonesian Name']]);

        $this->assertEquals('English Name', $project->getTranslation('name', 'en'));
        $this->assertEquals('Indonesian Name', $project->getTranslation('name', 'id'));
    }

    public function test_it_has_translatable_description(): void
    {
        $project = Project::factory()->create(['description' => ['en' => 'English Desc', 'id' => 'Indonesian Desc']]);

        $this->assertEquals('English Desc', $project->getTranslation('description', 'en'));
        $this->assertEquals('Indonesian Desc', $project->getTranslation('description', 'id'));
    }

    public function test_is_referenced_returns_true_if_has_partners(): void
    {
        $project = Project::factory()->create();
        $partner = Partner::factory()->create();

        $this->assertFalse($project->isReferenced());

        $project->partners()->attach($partner);

        $this->assertTrue($project->isReferenced());
    }
}
