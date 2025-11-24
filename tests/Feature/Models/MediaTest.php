<?php

namespace Tests\Feature\Models;

use App\Models\Media;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_cannot_be_deleted_if_referenced_by_project(): void
    {
        $media = Media::factory()->create();
        $project = Project::factory()->create([
            'logo_id' => $media->id,
        ]);

        $this->assertTrue($media->isReferenced());

        $project->delete();
        $media->refresh();

        $this->assertFalse($media->isReferenced());
    }

    public function test_media_cannot_be_deleted_if_referenced_by_partner(): void
    {
        $media = Media::factory()->create();
        $partner = Partner::factory()->create([
            'logo_id' => $media->id,
        ]);

        $this->assertTrue($media->isReferenced());

        $partner->delete();
        $media->refresh();

        $this->assertFalse($media->isReferenced());
    }

    public function test_media_can_be_deleted_if_not_referenced(): void
    {
        $media = Media::factory()->create();

        $this->assertFalse($media->isReferenced());
    }
}
