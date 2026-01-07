<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\DonorHealthProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = \App\Models\Governorate::all();

        $donors = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed.mohamed@example.com',
                'gender' => Donor::GENDER_MALE,
                'blood_type' => Donor::BLOOD_TYPE_O_POSITIVE,
                'national_id' => '100000001',
            ],
            [
                'name' => 'سارة علي',
                'email' => 'sarah.ali@example.com',
                'gender' => Donor::GENDER_FEMALE,
                'blood_type' => Donor::BLOOD_TYPE_A_POSITIVE,
                'national_id' => '100000002',
            ],
            [
                'name' => 'محمود حسن',
                'email' => 'mahmoud.hassan@example.com',
                'gender' => Donor::GENDER_MALE,
                'blood_type' => Donor::BLOOD_TYPE_B_POSITIVE,
                'national_id' => '100000003',
            ],
            [
                'name' => 'فاطمة إبراهيم',
                'email' => 'fatima.ibrahim@example.com',
                'gender' => Donor::GENDER_FEMALE,
                'blood_type' => Donor::BLOOD_TYPE_O_NEGATIVE,
                'national_id' => '100000004',
            ],
            [
                'name' => 'عمر خالد',
                'email' => 'omar.khaled@example.com',
                'gender' => Donor::GENDER_MALE,
                'blood_type' => Donor::BLOOD_TYPE_AB_POSITIVE,
                'national_id' => '100000005',
            ],
            [
                'name' => 'ليلى حسين',
                'email' => 'layla.hussein@example.com',
                'gender' => Donor::GENDER_FEMALE,
                'blood_type' => Donor::BLOOD_TYPE_A_NEGATIVE,
                'national_id' => '100000006',
            ],
            [
                'name' => 'يوسف عبدالله',
                'email' => 'youssef.abdallah@example.com',
                'gender' => Donor::GENDER_MALE,
                'blood_type' => Donor::BLOOD_TYPE_B_NEGATIVE,
                'national_id' => '100000007',
            ],
            [
                'name' => 'نور الدين',
                'email' => 'nour.eldin@example.com',
                'gender' => Donor::GENDER_MALE,
                'blood_type' => Donor::BLOOD_TYPE_AB_NEGATIVE,
                'national_id' => '100000008',
            ],
            [
                'name' => 'مصطفى عثمان',
                'email' => 'mustafa.othman@example.com',
                'gender' => Donor::GENDER_MALE,
                'blood_type' => Donor::BLOOD_TYPE_O_POSITIVE,
                'national_id' => '100000009',
            ],
            [
                'name' => 'مريم سعيد',
                'email' => 'mariam.saeed@example.com',
                'gender' => Donor::GENDER_FEMALE,
                'blood_type' => Donor::BLOOD_TYPE_A_POSITIVE,
                'national_id' => '100000010',
            ],
        ];

        foreach ($donors as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'phone' => '059' . substr($data['national_id'], -7),
                    'role' => User::ROLE_DONOR,
                ]
            );

            $donor = Donor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => $data['national_id'],
                    'governorate_id' => $governorates->isNotEmpty() ? $governorates->random()->id : null,
                    'gender' => $data['gender'],
                    'birth_date' => Carbon::now()->subYears(rand(20, 40)),
                    'lat' => 31.5 + (rand(0, 100) / 1000),
                    'lng' => 34.4 + (rand(0, 100) / 1000),
                ]
            );

            // Create or update Health Profile
            DonorHealthProfile::updateOrCreate(
                ['donor_id' => $donor->id],
                [
                    'weight' => rand(60, 90),
                    'height' => rand(160, 190),
                    'blood_type' => $data['blood_type'],
                    'is_eligible' => true,
                    'total_donations' => rand(0, 5),
                ]
            );
        }
    }
}
