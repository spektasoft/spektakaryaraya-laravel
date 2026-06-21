<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['en' => $this->faker->company(), 'id' => $this->faker->company()],
            'description' => ['en' => $this->faker->paragraph(), 'id' => $this->faker->paragraph()],
            'url' => $this->faker->url(),
            'logo_id' => Media::factory(),
            'creator_id' => User::factory(),
        ];
    }
}
