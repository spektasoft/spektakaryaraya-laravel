<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
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
            'logo_id' => \App\Models\Media::factory(),
        ];
    }
}
