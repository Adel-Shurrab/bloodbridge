<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestResponse>
 */
class RequestResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blood_request_id' => \App\Models\BloodRequest::factory(),
            'donor_id' => \App\Models\Donor::factory(),
            'status' => $this->faker->randomElement(array_keys(\App\Models\RequestResponse::getStatusOptions())),
            'verification_code' => $this->faker->numberBetween(100000, 999999),
            'verified_at' => null,
            'decline_reason' => null,
            'responded_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => \App\Models\RequestResponse::STATUS_COMPLETED,
            'verified_at' => now(),
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => \App\Models\RequestResponse::STATUS_DECLINED,
            'decline_reason' => $this->faker->sentence(),
        ]);
    }
}
