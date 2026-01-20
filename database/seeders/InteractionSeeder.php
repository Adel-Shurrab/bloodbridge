<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use Carbon\Carbon;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. سيناريو "سعيد الحداد" استجاب لطلب الشفاء (قريب منه) -> وتم التبرع بنجاح
        $donorSaeed = User::where('email', 'saeed@test.com')->first()->donor;
        $shifaRequest = BloodRequest::where('units_needed', 10)->first(); // طلب الشفاء العاجل

        RequestResponse::create([
            'blood_request_id' => $shifaRequest->id,
            'donor_id' => $donorSaeed->id,
            'status' => RequestResponseStatus::COMPLETED, // تبرع
            'responded_at' => Carbon::now()->subHours(1),
            'verified_at' => Carbon::now()->subMinutes(30),
            'verification_qr_code' => true,
        ]);

        // تحديث الطلب ليعكس قبول متبرع واحد
        $shifaRequest->increment('donors_accepted');
        $shifaRequest->increment('donors_completed');

        // 2. سيناريو "منير البردويل" (بدون GPS) استجاب لنفس طلب الشفاء -> ولكنه لم يصل بعد
        $donorMunir = User::where('email', 'munir@test.com')->first()->donor;

        RequestResponse::create([
            'blood_request_id' => $shifaRequest->id,
            'donor_id' => $donorMunir->id,
            'status' => RequestResponseStatus::PENDING, // وافق وفي الطريق
            'responded_at' => Carbon::now()->subMinutes(10),
        ]);
        $shifaRequest->increment('donors_accepted');

        // 3. سيناريو "يحيى" استجاب لطلب مستشفى ناصر -> ولكن تم رفضه طبياً في المستشفى
        $donorYahya = User::where('email', 'yahya@test.com')->first()->donor;
        $nasserRequest = BloodRequest::where('units_needed', 3)->first();

        RequestResponse::create([
            'blood_request_id' => $nasserRequest->id,
            'donor_id' => $donorYahya->id,
            'status' => RequestResponseStatus::DECLINED, // رفض طبي
            'responded_at' => Carbon::now()->subHours(2),
            'decline_reason' => 'انخفاض مفاجئ في ضغط الدم عند الفحص السريري',
            'verified_at' => Carbon::now()->subHours(1),
        ]);
        // ملاحظة: لا نزيد donors_completed لأنه رفض، ولكن نزيد donors_accepted لأنه استجاب
        $nasserRequest->increment('donors_accepted');
    }
}
