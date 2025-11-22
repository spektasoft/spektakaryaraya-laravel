<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }
}
