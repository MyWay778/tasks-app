<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement([
            TaskStatus::COMPLETED,
            TaskStatus::NOT_COMPLETED
        ]);

        return [
            'title' => $this->faker->sentence(3), // 3 случайных слова
            'description' => $this->faker->paragraph(), // Случайный абзац
            'status' => $status,
            'completed_at' => $status === TaskStatus::COMPLETED
                ? $this->faker->dateTimeBetween('-1 month', 'now')
                : null,
            'created_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
        ];
    }
}
