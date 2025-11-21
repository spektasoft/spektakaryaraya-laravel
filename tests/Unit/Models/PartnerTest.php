<?php

namespace Tests\Unit\Models;

use App\Models\Media;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_projects(): void
    {
        $partner = Partner::factory()->create();
        $project = Project::factory()->create();

        $partner->projects()->attach($project);

        $this->assertTrue($partner->projects->contains($project));
        $this->assertInstanceOf(Project::class, $partner->projects->first());
    }

    public function test_it_belongs_to_logo(): void
    {
        $media = Media::factory()->create();
        $partner = Partner::factory()->create(['logo_id' => $media->id]);

        $this->assertInstanceOf(Media::class, $partner->logo);
        $this->assertEquals($media->id, $partner->logo->id);
    }

    public function test_it_has_translatable_name(): void
    {
        $partner = Partner::factory()->create(['name' => ['en' => 'English Name', 'id' => 'Indonesian Name']]);

        $this->assertEquals('English Name', $partner->getTranslation('name', 'en'));
        $this->assertEquals('Indonesian Name', $partner->getTranslation('name', 'id'));
    }

    public function test_it_has_translatable_description(): void
    {
        $partner = Partner::factory()->create(['description' => ['en' => 'English Desc', 'id' => 'Indonesian Desc']]);

        $this->assertEquals('English Desc', $partner->getTranslation('description', 'en'));
        $this->assertEquals('Indonesian Desc', $partner->getTranslation('description', 'id'));
    }

    public function test_is_referenced_returns_true_if_has_projects(): void
    {
        $partner = Partner::factory()->create();
        $project = Project::factory()->create();

        $this->assertFalse($partner->isReferenced());

        $partner->projects()->attach($project);

        $this->assertTrue($partner->isReferenced());
    }
}
