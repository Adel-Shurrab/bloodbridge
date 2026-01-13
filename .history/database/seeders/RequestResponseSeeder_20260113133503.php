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
            // تحديد عدد عشوائي من الاستجابات لكل طلب (من 0 إلى 5)
            $responsesCount = rand(0, 5);

            // اختيار متبرعين عشوائيين لهذا الطلب
            $randomDonors = $donors->random(min($responsesCount, $donors->count()));

            foreach ($randomDonors as $donor) {
                // التحقق من عدم وجود استجابة مسبقة لنفس المتبرع على نفس الطلب
                if (\App\Models\RequestResponse::where('blood_request_id', $request->id)
                    ->where('donor_id', $donor->id)
                    ->exists()
                ) {
                    continue;
                }

                $status = $faker->randomElement(array_keys(\App\Models\RequestResponse::getStatusOptions()));
                $createdAt = $faker->dateTimeBetween('-2 months', 'now');

                $verifiedAt = null;
                $declineReason = null;
                $respondedAt = (clone $createdAt)->modify('+' . rand(1, 48) . ' hours');

                if ($status == \App\Models\RequestResponse::STATUS_COMPLETED) {
                    $verifiedAt = (clone $respondedAt)->modify('+' . rand(1, 24) . ' hours');
                }

                if ($status == \App\Models\RequestResponse::STATUS_DECLINED) {
                    $declineReason = $faker->randomElement([
                        'غير متواجد في المدينة حالياً',
                        'أعاني من تعب صحي بسيط',
                        'تبرعت مؤخراً (قبل أقل من 3 أشهر)',
                        'لا يمكنني الوصول إلى المركز حالياً',
                        'أسباب شخصية أخرى',
                    ]);
                }

                \App\Models\RequestResponse::create([
                    'blood_request_id' => $request->id,
                    'donor_id' => $donor->id,
                    'status' => $status,
                    'verification_qr_code' => $status == \App\Models\RequestResponse::STATUS_COMPLETED,
                    'verified_at' => $verifiedAt,
                    'decline_reason' => $declineReason,
                    'responded_at' => $respondedAt,
                    'created_at' => $createdAt,
                ]);
            }
        }
    }
}
