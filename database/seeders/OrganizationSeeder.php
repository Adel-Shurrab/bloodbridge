<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Governorate;
use App\Enums\UserRole;
use App\Enums\OrganizationStatus;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::ADMIN)->first();

        $hospitals = [
            [
                'name'        => 'مجمع الشفاء الطبي',
                'gov'         => 'غزة',
                'slug'        => 'al-shifa',
                'lat'         => 31.524589,
                'lng'         => 34.444439,
                'email_key'   => 'shifa',
                'phone'       => '0822820001',
                'manager'     => 'د. محمد أبو سلمية',
                'position'    => 'المدير العام',
                'street'      => 'شارع الوحدة، حي الرمال، غزة',
                'description' => 'أكبر المستشفيات في قطاع غزة، يضم أقسام متخصصة في الجراحة والطوارئ وبنك الدم.',
                'capacity'    => 50,
                'license'     => 'LIC-SHIFA-2025',
            ],
            [
                'name'        => 'مستشفى القدس (الهلال الأحمر)',
                'gov'         => 'غزة',
                'slug'        => 'al-quds',
                'lat'         => 31.512300,
                'lng'         => 34.435500,
                'email_key'   => 'quds',
                'phone'       => '0822820002',
                'manager'     => 'د. بشار مراد',
                'position'    => 'مدير المستشفى',
                'street'      => 'طريق صلاح الدين، تل الهوا، غزة',
                'description' => 'مستشفى تابع للهلال الأحمر الفلسطيني، يخدم سكان جنوب مدينة غزة.',
                'capacity'    => 30,
                'license'     => 'LIC-QUDS-2025',
            ],
            [
                'name'        => 'المستشفى الإندونيسي',
                'gov'         => 'شمال غزة',
                'slug'        => 'indonesian',
                'lat'         => 31.554000,
                'lng'         => 34.506000,
                'email_key'   => 'indo',
                'phone'       => '0822820003',
                'manager'     => 'د. عاطف الكحلوت',
                'position'    => 'مدير المستشفى',
                'street'      => 'شارع بيت لاهيا الرئيسي، بيت لاهيا، شمال غزة',
                'description' => 'مستشفى حكومي يخدم محافظة شمال غزة، ممول جزئياً من إندونيسيا.',
                'capacity'    => 25,
                'license'     => 'LIC-INDONESIAN-2025',
            ],
            [
                'name'        => 'مستشفى شهداء الأقصى',
                'gov'         => 'دير البلح',
                'slug'        => 'alaqsa',
                'lat'         => 31.417000,
                'lng'         => 34.354000,
                'email_key'   => 'aqsa',
                'phone'       => '0822820004',
                'manager'     => 'د. كمال خطاب',
                'position'    => 'مدير المستشفى',
                'street'      => 'شارع صلاح الدين، دير البلح، وسط القطاع',
                'description' => 'المستشفى الرئيسي في وسط قطاع غزة، يغطي محافظة دير البلح.',
                'capacity'    => 30,
                'license'     => 'LIC-ALAQSA-2025',
            ],
            [
                'name'        => 'مجمع ناصر الطبي',
                'gov'         => 'خانيونس',
                'slug'        => 'nasser',
                'lat'         => 31.354700,
                'lng'         => 34.292400,
                'email_key'   => 'nasser',
                'phone'       => '0822820005',
                'manager'     => 'د. ناهض أبو طعيمة',
                'position'    => 'المدير العام',
                'street'      => 'شارع مستشفى ناصر، خانيونس',
                'description' => 'المستشفى المرجعي لمحافظة خانيونس، يضم بنك دم متخصص وأقسام طوارئ.',
                'capacity'    => 40,
                'license'     => 'LIC-NASSER-2025',
            ],
            [
                'name'        => 'مستشفى غزة الأوروبي',
                'gov'         => 'خانيونس',
                'slug'        => 'european',
                'lat'         => 31.318000,
                'lng'         => 34.335000,
                'email_key'   => 'euro',
                'phone'       => '0822820006',
                'manager'     => 'د. يوسف العقاد',
                'position'    => 'مدير المستشفى',
                'street'      => 'طريق الفخاري، المواصي، جنوب خانيونس',
                'description' => 'مستشفى حديث ممول أوروبياً، يُعدّ من أكثر المستشفيات تجهيزاً في القطاع.',
                'capacity'    => 35,
                'license'     => 'LIC-EUROPEAN-2025',
            ],
            [
                'name'        => 'مستشفى الشهيد أبو يوسف النجار',
                'gov'         => 'رفح',
                'slug'        => 'najjar',
                'lat'         => 31.272000,
                'lng'         => 34.258000,
                'email_key'   => 'najjar',
                'phone'       => '0822820007',
                'manager'     => 'د. مروان الهمص',
                'position'    => 'مدير المستشفى',
                'street'      => 'حي تل السلطان، رفح',
                'description' => 'المستشفى الرئيسي في محافظة رفح، يستقبل حالات الطوارئ الجنوبية.',
                'capacity'    => 20,
                'license'     => 'LIC-NAJJAR-2025',
            ],
        ];

        $workingDays = [0, 1, 2, 3, 4];

        foreach ($hospitals as $data) {
            $gov = Governorate::where('name', $data['gov'])->first();

            $user = User::create([
                'name'      => $data['name'],
                'email'     => "admin@{$data['email_key']}.ps",
                'phone'     => $data['phone'],
                'password'  => Hash::make('password'),
                'role'      => UserRole::ORGANIZATION,
                'is_active' => true,
            ]);

            Organization::create([
                'user_id'                   => $user->id,
                'governorate_id'            => $gov?->id,
                'org_name'                  => $data['name'],
                'slug'                      => $data['slug'],
                'license_number'            => $data['license'],
                'responsible_person_name'   => $data['manager'],
                'responsible_person_position' => $data['position'],
                'responsible_person_email'  => "manager@{$data['email_key']}.ps",
                'contact_email'             => "contact@{$data['email_key']}.ps",
                'contact_phone'             => $data['phone'],
                'auto_location_address'     => $data['street'],
                'description'               => $data['description'],
                'lat'                       => $data['lat'],
                'lng'                       => $data['lng'],
                'approval_status'           => OrganizationStatus::APPROVED,
                'approved_at'               => now()->subMonths(3),
                'approved_by'               => $admin?->id,
                'opening_time'              => '08:00',
                'closing_time'              => '22:00',
                'working_days'              => $workingDays,
                'daily_capacity'            => $data['capacity'],
            ]);
        }
    }
}
