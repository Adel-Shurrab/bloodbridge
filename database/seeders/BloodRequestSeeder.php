<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\BloodRequest;
use App\Enums\BloodType;
use App\Enums\UrgencyLevel;
use App\Enums\BloodRequestStatus;
use Carbon\Carbon;

class BloodRequestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. طلب عاجل في مستشفى الشفاء (نشط حالياً)
        $shifa = Organization::where('slug', 'al-shifa')->first();
        BloodRequest::create([
            'organization_id' => $shifa->id,
            'blood_type' => BloodType::O_POSITIVE,
            'units_needed' => 10,
            'urgency_level' => UrgencyLevel::CRITICAL,
            'search_radius_km' => 5, // نطاق ضيق للطوارئ
            'lat' => $shifa->lat,
            'lng' => $shifa->lng,
            'location_address' => 'مبنى الجراحات التخصصي',
            'status' => BloodRequestStatus::BROADCASTED,
            'broadcasted_at' => Carbon::now(),
        ]);

        // 2. طلب عادي في مستشفى ناصر (نشط)
        $nasser = Organization::where('slug', 'nasser')->first();
        BloodRequest::create([
            'organization_id' => $nasser->id,
            'blood_type' => BloodType::A_NEGATIVE,
            'units_needed' => 3,
            'urgency_level' => UrgencyLevel::NORMAL,
            'search_radius_km' => 15, // نطاق أوسع
            'lat' => $nasser->lat,
            'lng' => $nasser->lng,
            'status' => BloodRequestStatus::BROADCASTED,
            'broadcasted_at' => Carbon::now()->subHours(2),
        ]);

        // 3. طلب قديم ومكتمل في الإندونيسي (لغرض الأرشيف والإحصائيات)
        $indo = Organization::where('slug', 'indonesian')->first();
        BloodRequest::create([
            'organization_id' => $indo->id,
            'blood_type' => BloodType::B_POSITIVE,
            'units_needed' => 5,
            'urgency_level' => UrgencyLevel::NORMAL,
            'lat' => $indo->lat,
            'lng' => $indo->lng,
            'status' => BloodRequestStatus::FULFILLED,
            'donors_accepted' => 5,
            'donors_completed' => 5,
            'broadcasted_at' => Carbon::now()->subDays(5),
            'fulfilled_at' => Carbon::now()->subDays(4),
        ]);
    }
}
