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
        User::factory()->create([
            'name' => 'مسؤول النظام - تجربة',
            'email' => 'admin@bloodbridge.ps',
            'phone' => '0599000000',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::ADMIN,
        ]);

        $this->call([
            OrganizationSeeder::class, 
            
            DonorSeeder::class,
            
            BloodRequestSeeder::class,
            
            InteractionSeeder::class,
        ]);
    }
}
