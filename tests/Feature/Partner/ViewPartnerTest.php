<?php

namespace Tests\Feature\Partner;

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPartnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_detail_page_loads_correctly(): void
    {
        $partner = Partner::factory()->create([
            'name' => ['en' => 'Test Partner'],
        ]);

        $response = $this->get(route('partners.show', $partner));

        $response->assertStatus(200);
        $response->assertSee('Test Partner');
    }
}
