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
        $shifa     = Organization::where('slug', 'al-shifa')->first();
        $quds      = Organization::where('slug', 'al-quds')->first();
        $indo      = Organization::where('slug', 'indonesian')->first();
        $aqsa      = Organization::where('slug', 'alaqsa')->first();
        $nasser    = Organization::where('slug', 'nasser')->first();
        $european  = Organization::where('slug', 'european')->first();
        $najjar    = Organization::where('slug', 'najjar')->first();

        BloodRequest::create([
            'organization_id'       => $shifa->id,
            'blood_type'            => BloodType::O_POSITIVE,
            'units_needed'          => 10,
            'urgency_level'         => UrgencyLevel::CRITICAL,
            'additional_notes'      => 'حادث سير مروع — عدة مصابين في حالة حرجة، نحتاج O+ بشكل عاجل للغرفة الجراحية.',
            'search_radius_km'      => 5,
            'actual_search_radius_km' => 10,
            'lat'                   => $shifa->lat,
            'lng'                   => $shifa->lng,
            'location_address'      => 'مبنى الجراحات التخصصي — الطابق الثاني',
            'status'                => BloodRequestStatus::BROADCASTED,
            'donors_accepted'       => 2,
            'donors_completed'      => 0,
            'broadcasted_at'        => Carbon::now()->subMinutes(30),
        ]);

        BloodRequest::create([
            'organization_id'       => $nasser->id,
            'blood_type'            => BloodType::A_NEGATIVE,
            'units_needed'          => 3,
            'urgency_level'         => UrgencyLevel::NORMAL,
            'additional_notes'      => 'مريض تعرض لعملية جراحية مجدولة، يحتاج A- للتعويض.',
            'search_radius_km'      => 15,
            'actual_search_radius_km' => 15,
            'lat'                   => $nasser->lat,
            'lng'                   => $nasser->lng,
            'location_address'      => 'قسم الدم — مجمع ناصر الطبي',
            'status'                => BloodRequestStatus::BROADCASTED,
            'donors_accepted'       => 2,
            'donors_completed'      => 0,
            'broadcasted_at'        => Carbon::now()->subHours(2),
        ]);

        BloodRequest::create([
            'organization_id'       => $indo->id,
            'blood_type'            => BloodType::B_POSITIVE,
            'units_needed'          => 5,
            'urgency_level'         => UrgencyLevel::NORMAL,
            'additional_notes'      => 'طالب ولادة مبكرة، التبرع مكتمل بنجاح.',
            'search_radius_km'      => 10,
            'actual_search_radius_km' => 10,
            'lat'                   => $indo->lat,
            'lng'                   => $indo->lng,
            'location_address'      => 'بنك الدم — المستشفى الإندونيسي',
            'status'                => BloodRequestStatus::FULFILLED,
            'donors_accepted'       => 4,
            'donors_completed'      => 3,
            'broadcasted_at'        => Carbon::now()->subDays(5),
            'fulfilled_at'          => Carbon::now()->subDays(4),
        ]);

        BloodRequest::create([
            'organization_id'       => $quds->id,
            'blood_type'            => BloodType::AB_POSITIVE,
            'units_needed'          => 2,
            'urgency_level'         => UrgencyLevel::CRITICAL,
            'additional_notes'      => 'مريض غسيل كلى يحتاج AB+ قبل الجلسة القادمة.',
            'search_radius_km'      => 20,
            'actual_search_radius_km' => 25,
            'lat'                   => $quds->lat,
            'lng'                   => $quds->lng,
            'location_address'      => 'وحدة غسيل الكلى — مستشفى القدس',
            'status'                => BloodRequestStatus::EXPIRED,
            'donors_accepted'       => 2,
            'donors_completed'      => 0,
            'broadcasted_at'        => Carbon::now()->subHours(5),
        ]);

        BloodRequest::create([
            'organization_id'       => $aqsa->id,
            'blood_type'            => BloodType::O_NEGATIVE,
            'units_needed'          => 8,
            'urgency_level'         => UrgencyLevel::CRITICAL,
            'additional_notes'      => 'رضيع مصاب في الولادة يحتاج عمليات نقل دم متعددة. O- نادرة — نرجو التواصل فوراً.',
            'search_radius_km'      => 30,
            'actual_search_radius_km' => 30,
            'lat'                   => $aqsa->lat,
            'lng'                   => $aqsa->lng,
            'location_address'      => 'وحدة العناية المركزة لحديثي الولادة — الأقصى',
            'status'                => BloodRequestStatus::BROADCASTED,
            'donors_accepted'       => 1,
            'donors_completed'      => 0,
            'broadcasted_at'        => Carbon::now()->subHours(1),
        ]);

        BloodRequest::create([
            'organization_id'       => $european->id,
            'blood_type'            => BloodType::A_POSITIVE,
            'units_needed'          => 4,
            'urgency_level'         => UrgencyLevel::NORMAL,
            'additional_notes'      => 'تجديد مخزون بنك الدم قبل الأسبوع القادم.',
            'search_radius_km'      => 10,
            'lat'                   => $european->lat,
            'lng'                   => $european->lng,
            'location_address'      => 'بنك دم مستشفى غزة الأوروبي',
            'status'                => BloodRequestStatus::PENDING,
            'donors_accepted'       => 0,
            'donors_completed'      => 0,
        ]);

        BloodRequest::create([
            'organization_id'       => $najjar->id,
            'blood_type'            => BloodType::B_NEGATIVE,
            'units_needed'          => 2,
            'urgency_level'         => UrgencyLevel::CRITICAL,
            'additional_notes'      => 'تم إلغاء الطلب — تم التدبّر مع بنك دم آخر قبل التنفيذ.',
            'search_radius_km'      => 15,
            'lat'                   => $najjar->lat,
            'lng'                   => $najjar->lng,
            'location_address'      => 'قسم الطوارئ — مستشفى أبو يوسف النجار',
            'status'                => BloodRequestStatus::CANCELLED,
            'donors_accepted'       => 0,
            'donors_completed'      => 0,
            'broadcasted_at'        => Carbon::now()->subDays(2),
        ]);

        BloodRequest::create([
            'organization_id'       => $shifa->id,
            'blood_type'            => BloodType::AB_NEGATIVE,
            'units_needed'          => 1,
            'urgency_level'         => UrgencyLevel::CRITICAL,
            'additional_notes'      => 'انتهت صلاحية الطلب — لم يُستجَب له بالشكل الكافي.',
            'search_radius_km'      => 50,
            'actual_search_radius_km' => 50,
            'lat'                   => $shifa->lat,
            'lng'                   => $shifa->lng,
            'location_address'      => 'غرفة العمليات رقم 3 — مجمع الشفاء الطبي',
            'status'                => BloodRequestStatus::EXPIRED,
            'donors_accepted'       => 0,
            'donors_completed'      => 0,
            'broadcasted_at'        => Carbon::now()->subDays(10),
        ]);
    }
}
