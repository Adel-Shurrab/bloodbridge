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
                'org_name' => 'مستشفى الشفاء الطبي',
                'description' => 'المجمع الطبي الأكبر في قطاع غزة، يقدم خدمات طبية متكاملة للطوارئ والجراحة.',
                'governorate' => 'غزة',
                'responsible_person' => 'د. محمد الأحمد',
                'position' => 'مدير المجمع',
                'opening_time' => null, // 24/7
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 200,
                'license' => 'ORG-GAZA-001',
            ],
            [
                'org_name' => 'مجمع ناصر الطبي',
                'description' => 'مركز طبي حيوي يخدم المنطقة الجنوبية، متخصص في أمراض القلب والجراحة العامة.',
                'governorate' => 'خانيونس',
                'responsible_person' => 'د. خالد اليوسف',
                'position' => 'المدير الطبي',
                'opening_time' => '08:00',
                'closing_time' => '20:00',
                'working_days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'daily_capacity' => 120,
                'license' => 'ORG-KHAN-002',
            ],
            [
                'org_name' => 'مركز صحي الرمال',
                'description' => 'مركز رعاية أولية يقدم خدمات التطعيم والعيادات الخارجية لسكان غزة.',
                'governorate' => 'غزة',
                'responsible_person' => 'أ. سارة حسن',
                'position' => 'رئيسة المركز',
                'opening_time' => '07:30',
                'closing_time' => '15:30',
                'working_days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'daily_capacity' => 80,
                'license' => 'ORG-REMAL-003',
            ],
            [
                'org_name' => 'جمعية أصدقاء المريض',
                'description' => 'مؤسسة خيرية طبية تهدف لتوفير رعاية صحية بأسعار رمزية للمواطنين.',
                'governorate' => 'دير البلح',
                'responsible_person' => 'د. محمود سعيد',
                'position' => 'المدير التنفيذي',
                'opening_time' => null, // 24/7
                'closing_time' => null,
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'daily_capacity' => 150,
                'license' => 'ORG-DEIR-004',
            ],
            [
                'org_name' => 'مستشفى الهلال الإماراتي',
                'description' => 'مستشفى متخصص في رعاية الأمومة والطفولة في المنطقة الجنوبية.',
                'governorate' => 'رفح',
                'responsible_person' => 'د. منى العبد',
                'position' => 'مديرة المستشفى',
                'opening_time' => '08:00',
                'closing_time' => '16:00',
                'working_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'daily_capacity' => 100,
                'license' => 'ORG-RAFAH-005',
            ],
        ];

        foreach ($orgs as $index => $data) {
            $governorate = $governorates->where('name', $data['governorate'])->first();
            $email = "org" . ($index + 1) . "@example.com";

            $user = User::create([
                'name' => $data['responsible_person'],
                'email' => $email,
                'password' => Hash::make('password'),
                'phone' => '0599' . str_pad($index + 10, 6, '0', STR_PAD_LEFT),
                'role' => User::ROLE_ORGANIZATION,
                'is_active' => true,
            ]);

            Organization::create([
                'user_id' => $user->id,
                'org_name' => $data['org_name'],
                'description' => $data['description'],
                'governorate_id' => $governorate ? $governorate->id : null,
                'license_number' => $data['license'],
                'responsible_person_position' => $data['position'],
                'contact_email' => $email,
                'contact_phone' => $user->phone,
                'street_address' => 'الشارع الرئيسي - ' . $data['governorate'],
                'opening_time' => $data['opening_time'],
                'closing_time' => $data['closing_time'],
                'working_days' => $data['working_days'],
                'daily_capacity' => $data['daily_capacity'],
                'approval_status' => Organization::STATUS_APPROVED,
            ]);
        }
    }
}
