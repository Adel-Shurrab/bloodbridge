<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'org_name' => $this->faker->company(),
            'slug' => fn(array $attributes) => \Illuminate\Support\Str::slug($attributes['org_name']),
            'description' => $this->faker->paragraph(),
            'license_number' => $this->faker->unique()->numerify('ORG-#####'),
            'contact_email' => $this->faker->unique()->companyEmail(),
            'contact_phone' => $this->faker->unique()->phoneNumber(),
            'responsible_person_name' => $this->faker->name(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'approval_status' => \App\Enums\OrganizationStatus::APPROVED,
        ];
    }
}
