<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            'شمال غزة',
            'غزة',
            'دير البلح',
            'خانيونس',
            'رفح',
        ];

        foreach ($governorates as $name) {
            Governorate::firstOrCreate(['name' => $name]);
        }
    }
}
