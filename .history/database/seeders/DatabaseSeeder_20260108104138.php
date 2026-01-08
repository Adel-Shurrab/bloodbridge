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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0599000000',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->call([
            OrganizationSeeder::class,
            DonorSeeder::class,
            BloodRequestSeeder::class,
        ]);

        // Create some pending organizations for the dashboard
        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'name' => "فرد/مؤسسة معلقة $i",
                'email' => "pending{$i}@example.com",
                'password' => bcrypt('password'),
                'phone' => '0598' . rand(100000, 999999),
                'role' => User::ROLE_ORGANIZATION,
            ]);

            \App\Models\Organization::create([
                'user_id' => $user->id,
                'org_name' => "مؤسسة قيد الانتظار $i",
                'approval_status' => \App\Models\Organization::STATUS_PENDING,
                'license_number' => "PEND-00$i",
                'contact_email' => $user->email,
                'contact_phone' => $user->phone,
            ]);
        }
    }
}
