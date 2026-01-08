<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BloodRequestSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            return;
        }

        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        // Generate 50 realistic requests over the last year
        for ($i = 0; $i < 50; $i++) {
            $org = $organizations->random();
            $createdAt = Carbon::now()->subDays(rand(0, 365));

            // Randomly pick a status (heavier weight on fulfilled/broadcasted for clean charts)
            $status = rand(0, 10) > 3 ? BloodRequest::STATUS_FULFILLED : rand(0, 5);

            BloodRequest::create([
                'organization_id' => $org->id,
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'units_needed' => rand(1, 10),
                'urgency_level' => rand(1, 4),
                'status' => $status,
                'deadline' => $createdAt->copy()->addDays(rand(1, 7)),
                'location_name' => 'موقع عشوائي بـ ' . $org->name,
                'lat' => 31.5017,
                'lng' => 34.4668,
                'search_radius' => 10,
                'notes' => 'طلب عينة بيانات حقيقية للتجربة',
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(rand(60, 10000)),
            ]);
        }
    }
}
