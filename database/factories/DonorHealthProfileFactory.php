<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonorHealthProfile>
 */
class DonorHealthProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donor_id' => \App\Models\Donor::factory(),
            'weight' => $this->faker->numberBetween(50, 120),
            'height' => $this->faker->numberBetween(150, 200),
            'chronic_disease' => false,
            'recent_donation' => false,
            'infection' => false,
            'is_eligible' => true,
            'has_recent_surgery' => false,
            'blood_type' => $this->faker->randomElement(\App\Enums\BloodType::cases())->value,
            'total_donations' => 0,
        ];
    }
}
