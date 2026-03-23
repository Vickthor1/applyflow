<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->jobTitle(),
            'company' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->city() . ', ' . $this->faker->state(),
            'link' => $this->faker->url(),
            'match_score' => $this->faker->numberBetween(30, 100),
            'salary' => 'R$ ' . $this->faker->numberBetween(5, 20) . '.000 - R$ ' . $this->faker->numberBetween(8, 30) . '.000',
            'job_type' => $this->faker->randomElement(['full-time', 'part-time', 'contract', 'freelance']),
            'source' => $this->faker->randomElement(['LinkedIn', 'Indeed', 'Remotive', 'The Muse', 'Adzuna', 'JSearch', 'Glassdoor']),
            'applied' => false,
            'application_status' => 'not_applied',
        ];
    }
}
