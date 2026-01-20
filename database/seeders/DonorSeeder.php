<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Donor;
use App\Models\DonorHealthProfile;
use App\Models\Governorate;
use App\Enums\UserRole;
use App\Enums\Gender;
use App\Enums\BloodType;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. متبرعون GPS (شمال غزة - قريب من الإندونيسي)
        $this->createDonor('كريم المصري', 'karim@test.com', 'شمال غزة', 31.552000, 34.504000, BloodType::O_POSITIVE, true);
        
        // 2. متبرعون GPS (غزة - الرمال - قريب من الشفاء)
        $this->createDonor('سعيد الحداد', 'saeed@test.com', 'غزة', 31.523000, 34.446000, BloodType::A_POSITIVE, true);
        
        // 3. متبرعون GPS (دير البلح - قريب من الأقصى)
        $this->createDonor('ماهر أبو دقة', 'maher@test.com', 'دير البلح', 31.419000, 34.352000, BloodType::B_NEGATIVE, true);

        // 4. متبرعون GPS (خانيونس - قريب من ناصر)
        $this->createDonor('يحيى السنوار', 'yahya@test.com', 'خانيونس', 31.353000, 34.290000, BloodType::AB_POSITIVE, true);

        // 5. متبرعون GPS (رفح - قريب من النجار)
        $this->createDonor('خليل الحية', 'khalil@test.com', 'رفح', 31.274000, 34.256000, BloodType::O_NEGATIVE, true);

        // 6. متبرعون "بدون GPS" (يعتمدون على المحافظة فقط)
        $this->createDonor('منير البردويل', 'munir@test.com', 'غزة', null, null, BloodType::A_NEGATIVE, true);
        $this->createDonor('رامي حمدان', 'rami@test.com', 'شمال غزة', null, null, BloodType::B_POSITIVE, true);

        // 7. متبرع "غير مؤهل" (تبرع حديثاً)
        $this->createDonor('حازم قاسم', 'hazem@test.com', 'غزة', 31.520000, 34.440000, BloodType::O_POSITIVE, false, 'recent');

        // 8. متبرع "غير مؤهل" (مرض مزمن)
        $this->createDonor('فوزي برهوم', 'fawzi@test.com', 'رفح', null, null, BloodType::AB_NEGATIVE, false, 'disease');
    }

    private function createDonor($name, $email, $govName, $lat, $lng, $bloodType, $isEligible, $ineligibleReason = null)
    {
        $gov = Governorate::where('name', $govName)->first();
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => '059' . rand(1000000, 9999999),
            'password' => Hash::make('password'),
            'role' => UserRole::DONOR,
            'is_active' => true,
        ]);

        $donor = Donor::create([
            'user_id' => $user->id,
            'governorate_id' => $gov->id,
            'national_id' => rand(400000000, 499999999),
            'gender' => Gender::MALE,
            'birth_date' => '1995-01-01',
            'lat' => $lat,
            'lng' => $lng,
            'auto_location_address' => $lat ? 'عنوان تلقائي من الخريطة' : null,
        ]);

        $profileData = [
            'donor_id' => $donor->id,
            'blood_type' => $bloodType,
            'weight' => 75,
            'height' => 175,
            'is_eligible' => $isEligible,
            'total_donations' => $isEligible ? 5 : 6,
        ];

        if ($ineligibleReason === 'recent') {
            $profileData['recent_donation'] = true;
            $profileData['last_donation_date'] = Carbon::now()->subDays(10);
        } elseif ($ineligibleReason === 'disease') {
            $profileData['chronic_disease'] = true;
        } else {
            $profileData['last_donation_date'] = Carbon::now()->subMonths(6);
        }

        DonorHealthProfile::create($profileData);
    }
}