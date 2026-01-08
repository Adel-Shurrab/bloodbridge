<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\Organization;
use App\Models\Donor;
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

        $bloodTypes = [
            Donor::BLOOD_TYPE_O_POSITIVE,
            Donor::BLOOD_TYPE_O_NEGATIVE,
            Donor::BLOOD_TYPE_A_POSITIVE,
            Donor::BLOOD_TYPE_A_NEGATIVE,
            Donor::BLOOD_TYPE_B_POSITIVE,
            Donor::BLOOD_TYPE_B_NEGATIVE,
            Donor::BLOOD_TYPE_AB_POSITIVE,
            Donor::BLOOD_TYPE_AB_NEGATIVE,
        ];

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
                'search_radius_km' => rand(5, 20),
                'additional_notes' => 'طلب عينة بيانات حقيقية للتجربة',
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(rand(60, 10000)),
                'broadcasted_at' => rand(0, 1) ? $createdAt->copy()->addMinutes(rand(5, 30)) : null,
                'fulfilled_at' => $status === BloodRequest::STATUS_FULFILLED ? $createdAt->copy()->addHours(rand(1, 48)) : null,
            ]);
        }
    }
}
