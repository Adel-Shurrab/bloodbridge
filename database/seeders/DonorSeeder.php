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
use Faker\Factory as Faker;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA'); // بيانات عربية
        $password = Hash::make('password');

        // حدود تقريبية للمحافظات (Latitude/Longitude Bounds)
        // لضمان أن النقاط تقع فعلاً داخل المدن
        $govBounds = [
            'شمال غزة' => ['min_lat' => 31.540, 'max_lat' => 31.590, 'min_lng' => 34.480, 'max_lng' => 34.540],
            'غزة'      => ['min_lat' => 31.480, 'max_lat' => 31.540, 'min_lng' => 34.420, 'max_lng' => 34.480],
            'دير البلح' => ['min_lat' => 31.400, 'max_lat' => 31.450, 'min_lng' => 34.330, 'max_lng' => 34.380],
            'خانيونس'  => ['min_lat' => 31.320, 'max_lat' => 31.380, 'min_lng' => 34.270, 'max_lng' => 34.330],
            'رفح'      => ['min_lat' => 31.250, 'max_lat' => 31.300, 'min_lng' => 34.220, 'max_lng' => 34.280],
        ];

        // إنشاء 10 متبرعين لكل محافظة (المجموع 50)
        foreach ($govBounds as $govName => $bounds) {
            $governorate = Governorate::where('name', $govName)->first();

            if (!$governorate) continue;

            for ($i = 0; $i < 10; $i++) {
                // 1. تحديد هل المتبرع لديه GPS أم لا (80% نعم)
                $hasGps = rand(1, 10) <= 8;

                $lat = null;
                $lng = null;

                if ($hasGps) {
                    // توليد إحداثيات عشوائية ولكن "داخل حدود المحافظة"
                    $lat = $faker->randomFloat(6, $bounds['min_lat'], $bounds['max_lat']);
                    $lng = $faker->randomFloat(6, $bounds['min_lng'], $bounds['max_lng']);
                }

                // 2. إنشاء المستخدم
                $user = User::create([
                    'name' => $faker->name('male'), // أسماء عربية
                    'email' => $faker->unique()->userName . rand(100, 999) . '@example.com',
                    'phone' => '059' . $faker->unique()->numberBetween(1000000, 9999999),
                    'password' => $password,
                    'role' => UserRole::DONOR,
                    'is_active' => true,
                ]);

                // 3. إنشاء المتبرع
                $donor = Donor::create([
                    'user_id' => $user->id,
                    'governorate_id' => $governorate->id,
                    'national_id' => $faker->unique()->numberBetween(400000000, 499999999),
                    'gender' => Gender::MALE,
                    'birth_date' => $faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
                    'lat' => $lat,
                    'lng' => $lng,
                    'auto_location_address' => $lat ? "شارع {$faker->streetName}، $govName" : null,
                ]);

                // 4. الملف الصحي
                // 90% مؤهلين، 10% غير مؤهلين
                $isEligible = rand(1, 100) <= 90;
                $bloodType = $faker->randomElement(BloodType::cases());

                DonorHealthProfile::create([
                    'donor_id' => $donor->id,
                    'blood_type' => $bloodType,
                    'weight' => $faker->numberBetween(60, 95),
                    'height' => $faker->numberBetween(165, 190),
                    'is_eligible' => $isEligible,
                    'chronic_disease' => !$isEligible, // إذا غير مؤهل نفترض مرض مزمن للتبسيط
                    'total_donations' => $isEligible ? rand(0, 10) : 0,
                    'last_donation_date' => $isEligible ? $faker->dateTimeBetween('-2 years', '-4 months') : null,
                ]);
            }
        }
    }
}
