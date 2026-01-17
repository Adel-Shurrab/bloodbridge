<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\Organization;
use App\Models\Donor;
use App\Enums\BloodType;
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
            BloodType::O_POSITIVE,
            BloodType::O_NEGATIVE,
            BloodType::A_POSITIVE,
            BloodType::A_NEGATIVE,
            BloodType::B_POSITIVE,
            BloodType::B_NEGATIVE,
            BloodType::AB_POSITIVE,
            BloodType::AB_NEGATIVE,
        ];

        $notes = [
            'حالة طارئة - عملية جراحية بالقلب غداً صباحاً',
            'مريض ثلاسيميا بحاجة لنقل دم دوري',
            'حالة ولادة متعسرة - نزيف حاد',
            'فشل كلوي - بحاجة لوحدات دم لرفع الهيموجلوبين',
            'إصابة حادث سير - نزيف داخلي بحاجة ماسة',
            'عملية عظام مجدولة - تأمين وحدات دم احتياطية',
            'حالة سرطان دم - بحاجة لصفائح دموية',
            'عملية زراعة كلى - مطلوب تأمين فصيلة نادرة',
        ];

        // Generate 50 realistic requests over the last year
        for ($i = 0; $i < 50; $i++) {
            $org = $organizations->random();
            $createdAt = Carbon::now()->subDays(rand(0, 60)); // More recent focus

            $status = rand(0, 10) > 4 ? BloodRequest::STATUS_FULFILLED : BloodRequest::STATUS_BROADCASTED;

            BloodRequest::create([
                'organization_id' => $org->id,
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'units_needed' => rand(2, 12),
                'urgency_level' => rand(1, 4),
                'status' => $status,
                'search_radius_km' => rand(10, 40),
                'additional_notes' => $notes[array_rand($notes)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(rand(60, 10000)),
                'broadcasted_at' => $createdAt->copy()->addMinutes(rand(5, 30)),
                'fulfilled_at' => $status === BloodRequest::STATUS_FULFILLED ? $createdAt->copy()->addHours(rand(1, 72)) : null,
            ]);
        }
    }
}
