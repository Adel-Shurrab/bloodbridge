<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use Carbon\Carbon;
use Faker\Factory as Faker;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // جلب كل الطلبات النشطة أو المكتملة
        $requests = BloodRequest::all();

        foreach ($requests as $request) {
            // سنجعل لكل طلب ما بين 5 إلى 15 متبرع مستجيب
            $responsesCount = rand(5, 15);

            // جلب متبرعين عشوائيين (يفضل أن يكونوا نفس فصيلة الدم ليكون منطقياً)
            // ملاحظة: في حالات الطوارئ O- يعطي الجميع، لكن للتبسيط هنا سنجلب نفس الفصيلة
            $potentialDonors = Donor::whereHas('healthProfile', function ($q) use ($request) {
                $q->where('blood_type', $request->blood_type)
                    ->where('is_eligible', true);
            })->inRandomOrder()->take($responsesCount)->get();

            // إذا لم نجد كفاية، خذ أي متبرعين (محاكاة لضغط الناس)
            if ($potentialDonors->count() < $responsesCount) {
                $extraDonors = Donor::inRandomOrder()->take($responsesCount - $potentialDonors->count())->get();
                $potentialDonors = $potentialDonors->merge($extraDonors);
            }

            foreach ($potentialDonors as $donor) {
                // تحديد حالة الاستجابة عشوائياً بناءً على حالة الطلب
                $status = RequestResponseStatus::PENDING;
                $verifiedAt = null;
                $declineReason = null;

                $rand = rand(1, 100);

                if ($request->status === \App\Enums\BloodRequestStatus::FULFILLED) {
                    // إذا الطلب مكتمل، معظم الاستجابات تكون مكتملة أو مرفوضة (وصلوا متأخرين)
                    if ($rand <= 40) {
                        $status = RequestResponseStatus::COMPLETED;
                        $verifiedAt = Carbon::now()->subHours(rand(1, 24));
                        $request->increment('donors_accepted');
                        $request->increment('donors_completed');
                    } elseif ($rand <= 70) {
                        $status = RequestResponseStatus::DECLINED; // رفض في المستشفى (استبعاد طبي)
                        $verifiedAt = Carbon::now();
                        $declineReason = 'هيموجلوبين منخفض';
                        $request->increment('donors_accepted');
                    } else {
                        $status = RequestResponseStatus::NO_SHOW; // سجل وما اجاش (لم يحضر)
                        $request->increment('donors_accepted');
                    }
                } else {
                    // إذا الطلب لسا شغال
                    if ($rand <= 50) {
                        $status = RequestResponseStatus::PENDING; // لسا في الطريق
                        $request->increment('donors_accepted');
                    } elseif ($rand <= 80) {
                        $status = RequestResponseStatus::ACCEPTED; // وصل المستشفى وبستنى الدور (حضر)
                        $request->increment('donors_accepted');
                    } else {
                        $status = RequestResponseStatus::IGNORED; // تجاهل/ألغى الطلب بنفسه
                        $declineReason = 'ظرف طارئ منعني من الحضور';
                    }
                }

                // منع التكرار (لأن الـ Factory ممكن يختار نفس المتبرع لطلب آخر بالصدفة)
                // في Seeder بسيط نستخدم firstOrCreate
                RequestResponse::firstOrCreate([
                    'blood_request_id' => $request->id,
                    'donor_id' => $donor->id,
                ], [
                    'status' => $status,
                    'responded_at' => Carbon::now()->subMinutes(rand(10, 300)),
                    'verified_at' => $verifiedAt,
                    'verification_qr_code' => ($status === RequestResponseStatus::COMPLETED || $status === RequestResponseStatus::ACCEPTED),
                    'decline_reason' => $declineReason,
                ]);
            }
        }
    }
}
