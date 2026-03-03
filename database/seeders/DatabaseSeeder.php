<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Super admin
        User::factory()->create([
            'name'     => 'مسؤول النظام',
            'email'    => 'admin@bloodbridge.ps',
            'phone'    => '0599000000',
            'password' => bcrypt('password'),
            'role'     => \App\Enums\UserRole::ADMIN,
        ]);

        $this->call([
            OrganizationSeeder::class,  // 7 Gaza hospitals
            DonorSeeder::class,         // 25 fixed donors
            BloodRequestSeeder::class,  // 8 blood requests (all statuses)
            InteractionSeeder::class,   // Fixed donor-request responses (all response statuses)
        ]);
    }
}
