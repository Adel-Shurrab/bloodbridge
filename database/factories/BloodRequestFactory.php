<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BloodRequest>
 */
class BloodRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'blood_type' => $this->faker->randomElement(\App\Enums\BloodType::cases()),
            'units_needed' => $this->faker->numberBetween(1, 10),
            'urgency_level' => \App\Enums\UrgencyLevel::MEDIUM,
            'search_radius_km' => 10,
            'lat' => \App\Constants\PalestineCoordinates::GAZA['lat'],
            'lng' => \App\Constants\PalestineCoordinates::GAZA['lng'],
            'status' => \App\Enums\BloodRequestStatus::PENDING,
        ];
    }
}
