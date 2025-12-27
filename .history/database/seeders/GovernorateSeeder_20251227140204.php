<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = ['غزة', 'خانيونس', 'شمال غزة', 'دير البلح', 'رفح'];

        foreach ($governorates as $name) {
            \App\Models\Governorate::updateOrCreate(['name' => $name]);
        }
    }
}
