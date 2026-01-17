<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\DonorHealthProfile;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['محمد', 'أحمد', 'محمود', 'عبد الله', 'يوسف', 'خالد', 'إبراهيم', 'عمر', 'علي', 'حسين', 'سارة', 'فاطمة', 'مريم', 'ليلى', 'نور', 'منى', 'أسماء', 'رشا', 'هبة', 'منة'];
        $lastNames = ['سليمان', 'أبو كمال', 'الحسن', 'عوض', 'صالحة', 'النجار', 'المصري', 'حمودة', 'عبيد', 'شاهين', 'بركة', 'عقل', 'داوود', 'خليل', 'ياسين', 'الأسطل', 'قدرة', 'صيام', 'أبو حطب', 'الأطرش'];

        $orgIds = Organization::pluck('id')->toArray();

        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName . ' ' . $lastName;
            $email = "donor{$i}@bloodbridge.ps";
            $nationalId = '4' . str_pad($i, 8, '0', STR_PAD_LEFT);
            $gender = in_array($firstName, ['سارة', 'فاطمة', 'مريم', 'ليلى', 'نور', 'منى', 'أسماء', 'رشا', 'هبة', 'منة']) ? Donor::GENDER_FEMALE : Donor::GENDER_MALE;

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'phone' => '0595' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'role' => User::ROLE_DONOR,
                'is_active' => true,
            ]);

            $donor = Donor::create([
                'user_id' => $user->id,
                'national_id' => $nationalId,
                'gender' => $gender,
                'birth_date' => Carbon::now()->subYears(rand(18, 60)),
                'lat' => 31.5 + (rand(-200, 200) / 1000),
                'lng' => 34.4 + (rand(-100, 100) / 1000),
                'governorate_id' => \App\Models\Governorate::inRandomOrder()->first()?->id,
            ]);

            // Create Health Profile with varied data
            $hasRecentDonation = rand(0, 10) > 7;
            $hasSurgery = rand(0, 10) > 8;

            DonorHealthProfile::create([
                'donor_id' => $donor->id,
                'weight' => rand(55, 110),
                'height' => rand(155, 195),
                'blood_type' => rand(1, 8),
                'verified_blood_type' => $hasVerifiedBloodType = (rand(0, 10) > 6 ? rand(1, 8) : null),
                'verified_by_organization_id' => $hasVerifiedBloodType && !empty($orgIds) ? $orgIds[array_rand($orgIds)] : null,
                'verified_at' => $hasVerifiedBloodType ? Carbon::now()->subDays(rand(5, 365)) : null,
                'chronic_disease' => rand(0, 10) > 9,
                'recent_donation' => $hasRecentDonation,
                'last_donation_date' => $hasRecentDonation ? Carbon::now()->subDays(rand(10, 150)) : null,
                'has_recent_surgery' => $hasSurgery,
                'surgery_date' => $hasSurgery ? Carbon::now()->subDays(rand(5, 60)) : null,
                'total_donations' => rand(0, 20),
                'is_eligible' => true, // Will be auto-calculated by model booted method
            ]);
        }
    }
}
