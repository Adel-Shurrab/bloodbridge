<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = Governorate::all();

        if ($governorates->isEmpty()) {
            return;
        }

        $orgs = [
            [
                'org_name' => 'مجمع الشفاء الطبي',
                'slug' => 'al-shifa-medical-complex',
                'description' => 'المستشفى الأكبر في قطاع غزة، يقدم خدمات تخصصية متكاملة وبنك دم مركزي يعمل على مدار الساعة.',
                'governorate' => 'غزة',
                'responsible_person' => 'د. محمد صبحي سليم',
                'position' => 'مدير عام المجمع',
                'opening_time' => null,
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 500,
                'license' => 'MOH-GZ-001',
            ],
            [
                'org_name' => 'مجمع ناصر الطبي',
                'slug' => 'nasser-medical-complex',
                'description' => 'مركز طبي رائد يخدم المنطقة الجنوبية، مجهز بأحدث تقنيات نقل الدم والجراحة.',
                'governorate' => 'خانيونس',
                'responsible_person' => 'د. عاطف الحوت',
                'position' => 'المدير الطبي',
                'opening_time' => null,
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 300,
                'license' => 'MOH-KH-002',
            ],
            [
                'org_name' => 'المستشفى الإندونيسي',
                'slug' => 'indonesian-hospital',
                'description' => 'مرفق طبي حيوي يخدم منطقة شمال غزة، متخصص في الطوارئ والجراحة العامة.',
                'governorate' => 'شمال غزة',
                'responsible_person' => 'د. شوقي سالم',
                'position' => 'مدير المستشفى',
                'opening_time' => null,
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 200,
                'license' => 'MOH-NG-003',
            ],
            [
                'org_name' => 'مستشفى شهداء الأقصى',
                'slug' => 'al-aqsa-martyrs-hospital',
                'description' => 'المركز الطبي الرئيسي في المنطقة الوسطى، يقدم خدمات الرعاية الصحية الشاملة وحملات التبرع بالدم.',
                'governorate' => 'دير البلح',
                'responsible_person' => 'د. إياد كمال الجبري',
                'position' => 'المدير العام',
                'opening_time' => null,
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 250,
                'license' => 'MOH-DB-004',
            ],
            [
                'org_name' => 'مستشفى أبو يوسف النجار',
                'slug' => 'abu-yousef-al-najjar-hospital',
                'description' => 'يخدم مدينة رفح والمناطق الحدودية، يوفر خدمات الرعاية الأولية وبنك دم فرعي.',
                'governorate' => 'رفح',
                'responsible_person' => 'د. كامل صيام',
                'position' => 'المدير الطبي',
                'opening_time' => '08:00',
                'closing_time' => '22:00',
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'daily_capacity' => 150,
                'license' => 'MOH-RF-005',
            ],
            [
                'org_name' => 'مستشفى القدس - الهلال الأحمر',
                'slug' => 'al-quds-hospital',
                'description' => 'يتبع لجمعية الهلال الأحمر الفلسطيني، متميز في خدمات الجراحة والمختبرات المتقدمة.',
                'governorate' => 'غزة',
                'responsible_person' => 'د. بشار مراد',
                'position' => 'مدير الإسعاف والطوارئ',
                'opening_time' => null,
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 180,
                'license' => 'PRCS-GZ-006',
            ],
            [
                'org_name' => 'مركز صحي الرمال المركزي',
                'slug' => 'al-remal-health-center',
                'description' => 'أقدم وأكبر مراكز الرعاية الأولية في مدينة غزة، يشرف على حملات التبرع الميدانية.',
                'governorate' => 'غزة',
                'responsible_person' => 'أ. صهيب الهسي',
                'position' => 'مدير المركز',
                'opening_time' => '07:30',
                'closing_time' => '15:30',
                'working_days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'daily_capacity' => 100,
                'license' => 'PHC-GZ-007',
            ],
            [
                'org_name' => 'مستشفى العودة - جباليا',
                'slug' => 'al-awda-hospital',
                'description' => 'مؤسسة طبية أهلية تقدم خدمات جراحة العظام والسمعيات وبنك دم متخصص.',
                'governorate' => 'شمال غزة',
                'responsible_person' => 'د. رأفت المجدلاوي',
                'position' => 'مدير المجمع',
                'opening_time' => null,
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 120,
                'license' => 'AWDA-NG-008',
            ],
            [
                'org_name' => 'المستشفى الكويتي التخصصي',
                'slug' => 'kuwaiti-specialist-hospital',
                'description' => 'مستشفى خاص يقدم خدمات طبية متميزة في رفح، مجهز بمختبرات حديثة.',
                'governorate' => 'رفح',
                'responsible_person' => 'د. صهيب الهمص',
                'position' => 'المدير العام',
                'opening_time' => '08:00',
                'closing_time' => '23:00',
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 80,
                'license' => 'PVT-RF-009',
            ],
        ];

        foreach ($orgs as $index => $data) {
            $governorate = $governorates->where('name', $data['governorate'])->first();
            $email = "org" . ($index + 1) . "@bloodbridge.ps";

            $user = User::create([
                'name' => $data['responsible_person'],
                'email' => $email,
                'password' => Hash::make('password'),
                'phone' => '0599' . str_pad($index + 10, 6, '0', STR_PAD_LEFT),
                'role' => \App\Enums\UserRole::ORGANIZATION,
                'is_active' => true,
            ]);

            Organization::create([
                'user_id' => $user->id,
                'org_name' => $data['org_name'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'governorate_id' => $governorate ? $governorate->id : null,
                'license_number' => $data['license'],
                'responsible_person_name' => $data['responsible_person'],
                'responsible_person_position' => $data['position'],
                'responsible_person_email' => $email,
                'contact_email' => $email,
                'contact_phone' => $user->phone,
                'street_address' => 'الشارع الرئيسي - ' . $data['governorate'],
                'opening_time' => $data['opening_time'],
                'closing_time' => $data['closing_time'],
                'working_days' => $data['working_days'],
                'daily_capacity' => $data['daily_capacity'],
                'approval_status' => \App\Enums\OrganizationStatus::APPROVED,
            ]);
        }
    }
}
