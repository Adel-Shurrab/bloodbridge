<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // استخدام Faker بالعربية
        $faker = \Faker\Factory::create('ar_SA');

        $bloodRequests = \App\Models\BloodRequest::all();
        $donors = \App\Models\Donor::all();

        if ($bloodRequests->isEmpty()) {
            $this->command->info('لا توجد طلبات دم. جاري إنشاء 10 طلبات دم...');
            $bloodRequests = \App\Models\BloodRequest::factory()->count(10)->create();
        }

        if ($donors->isEmpty()) {
            $this->command->info('لا يوجد متبرعين. جاري إنشاء 20 متبرع...');
            $donors = \App\Models\Donor::factory()->count(20)->create();
        }

        foreach ($bloodRequests as $request) {
            $responsesCount = rand(2, 6); // At least some responses for each request
            $randomDonors = $donors->random(min($responsesCount, $donors->count()));

            foreach ($randomDonors as $donor) {
                if (\App\Models\RequestResponse::where('blood_request_id', '=', $request->id, 'and')
                    ->where('donor_id', '=', $donor->id, 'and')
                    ->exists()
                ) {
                    continue;
                }

                // Status logic: Most are accepted/completed, some pending, some excluded
                $status = $faker->randomElement([
                    \App\Enums\RequestResponseStatus::PENDING,
                    \App\Enums\RequestResponseStatus::ACCEPTED,
                    \App\Enums\RequestResponseStatus::ACCEPTED,
                    \App\Enums\RequestResponseStatus::COMPLETED,
                    \App\Enums\RequestResponseStatus::COMPLETED,
                    \App\Enums\RequestResponseStatus::DECLINED, // Medical Exclusion
                    \App\Enums\RequestResponseStatus::NO_SHOW,
                ]);

                // 1. Creation of request is the baseline
                $requestCreated = $request->created_at;

                // 2. Response (Application) is 5 mins to 24 hours after request
                $respondedAt = (clone $requestCreated)->modify('+' . rand(5, 1440) . ' minutes');

                $verifiedAt = null;
                $declineReason = null;

                // 3. Verification happens ONLY for Completed or Medically Excluded
                // It must be AFTER responded_at
                if ($status === \App\Enums\RequestResponseStatus::COMPLETED || $status === \App\Enums\RequestResponseStatus::DECLINED) {
                    $verifiedAt = (clone $respondedAt)->modify('+' . rand(30, 1440) . ' minutes');
                }

                if ($status === \App\Enums\RequestResponseStatus::DECLINED) {
                    $declineReason = $faker->randomElement([
                        'استبعاد طبي: نقص حاد في الهيموجلوبين',
                        'استبعاد طبي: انخفاض ضغط الدم',
                        'استبعاد طبي: وجود أعراض إنفلونزا حادة',
                        'استبعاد طبي: تاريخ حديث لإجراء جراحي',
                    ]);
                }

                \App\Models\RequestResponse::create([
                    'blood_request_id' => $request->id,
                    'donor_id' => $donor->id,
                    'status' => $status,
                    'verification_qr_code' => $status === \App\Enums\RequestResponseStatus::COMPLETED,
                    'verified_at' => $verifiedAt,
                    'decline_reason' => $declineReason,
                    'responded_at' => $respondedAt,
                    'created_at' => $respondedAt, // Record of response creation
                ]);
            }
        }
    }
}
