<?php

namespace Database\Factories;

use App\Enums\Project\Status;
use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
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
            'creator_id' => User::factory(),
            'name' => ['en' => $this->faker->sentence(3), 'id' => $this->faker->sentence(3)],
            'description' => ['en' => $this->faker->paragraph(), 'id' => $this->faker->paragraph()],
            'status' => $this->faker->randomElement(Status::cases()),
            'start_date' => $this->faker->date(),
            'url' => $this->faker->url(),
            'logo_id' => Media::factory(),
        ];
    }
}
