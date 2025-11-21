<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['en' => $this->faker->sentence(3), 'id' => $this->faker->sentence(3)],
            'description' => ['en' => $this->faker->paragraph(), 'id' => $this->faker->paragraph()],
            'status' => $this->faker->randomElement(\App\Enums\Project\Status::cases()),
            'start_date' => $this->faker->date(),
            'url' => $this->faker->url(),
            'logo_id' => \App\Models\Media::factory(),
        ];
    }
}
