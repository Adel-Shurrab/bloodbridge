<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Donor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'national_id' => $this->faker->unique()->numerify('#########'), // 9 digits
            'gender' => $this->faker->randomElement(Donor::GENDER_LIST),
            'birth_date' => $this->faker->date(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'points' => 0,
            'level' => 1,
        ];
    }
}
