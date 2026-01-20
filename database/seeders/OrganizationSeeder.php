<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Governorate;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'مجمع الشفاء الطبي',
                'gov' => 'غزة',
                'slug' => 'al-shifa',
                'lat' => 31.524589,
                'lng' => 34.444439,
                'email' => 'shifa',
                'phone' => '082820001',
                'manager' => 'د. محمد أبو سلمية'
            ],
            [
                'name' => 'مستشفى القدس (الهلال الأحمر)',
                'gov' => 'غزة',
                'slug' => 'al-quds',
                'lat' => 31.512300,
                'lng' => 34.435500, // تل الهوا
                'email' => 'quds',
                'phone' => '082820002',
                'manager' => 'د. بشار مراد'
            ],
            [
                'name' => 'المستشفى الإندونيسي',
                'gov' => 'شمال غزة',
                'slug' => 'indonesian',
                'lat' => 31.554000,
                'lng' => 34.506000, // بيت لاهيا
                'email' => 'indo',
                'phone' => '082820003',
                'manager' => 'د. عاطف الكحلوت'
            ],
            [
                'name' => 'مستشفى شهداء الأقصى',
                'gov' => 'دير البلح',
                'slug' => 'alaqsa',
                'lat' => 31.417000,
                'lng' => 34.354000, // دير البلح
                'email' => 'aqsa',
                'phone' => '082820004',
                'manager' => 'د. كمال خطاب'
            ],
            [
                'name' => 'مجمع ناصر الطبي',
                'gov' => 'خانيونس',
                'slug' => 'nasser',
                'lat' => 31.354700,
                'lng' => 34.292400, // خانيونس
                'email' => 'nasser',
                'phone' => '082820005',
                'manager' => 'د. ناهض أبو طعيمة'
            ],
            [
                'name' => 'مستشفى غزة الأوروبي',
                'gov' => 'خانيونس', // يقع شرق خانيونس
                'slug' => 'european',
                'lat' => 31.318000,
                'lng' => 34.335000, // الفخاري
                'email' => 'euro',
                'phone' => '082820006',
                'manager' => 'د. يوسف العقاد'
            ],
            [
                'name' => 'مستشفى الشهيد أبو يوسف النجار',
                'gov' => 'رفح',
                'slug' => 'najjar',
                'lat' => 31.272000,
                'lng' => 34.258000, // رفح
                'email' => 'najjar',
                'phone' => '082820007',
                'manager' => 'د. مروان الهمص'
            ],
        ];

        foreach ($hospitals as $data) {
            $gov = Governorate::where('name', $data['gov'])->first();

            $user = User::create([
                'name' => $data['name'],
                'email' => "admin@{$data['email']}.ps",
                'phone' => "059{$data['phone']}", // رقم وهمي للسييدر
                'password' => Hash::make('password'),
                'role' => UserRole::ORGANIZATION,
                'is_active' => true,
            ]);

            Organization::create([
                'user_id' => $user->id,
                'governorate_id' => $gov->id,
                'org_name' => $data['name'],
                'slug' => $data['slug'],
                'license_number' => 'LIC-' . strtoupper($data['slug']) . '-2025',
                'responsible_person_name' => $data['manager'],
                'responsible_person_position' => 'مدير المستشفى',
                'contact_email' => "contact@{$data['email']}.ps",
                'contact_phone' => $data['phone'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'approval_status' => \App\Enums\OrganizationStatus::APPROVED,
                'opening_time' => '08:00',
                'closing_time' => '22:00',
            ]);
        }
    }
}
