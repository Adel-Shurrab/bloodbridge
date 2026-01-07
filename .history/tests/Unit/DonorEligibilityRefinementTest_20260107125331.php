<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DonorHealthProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Donor;
use App\Models\Governorate;

class DonorEligibilityRefinementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create([
            'name' => 'Test Donor',
            'email' => 'test@example.com',
            'phone' => '123456789',
            'password' => 'password',
            'role' => User::ROLE_DONOR,
        ]);

        $governorate = Governorate::create(['name' => 'Test Governorate']);

        $this->donor = Donor::create([
            'user_id' => $user->id,
            'governorate_id' => $governorate->id,
            'national_id' => '123456789',
            'gender' => Donor::GENDER_MALE,
            'birth_date' => '1990-01-01',
        ]);
    }

    public function test_clearing_donation_flag_removes_date_and_restores_eligibility()
    {
        $profile = DonorHealthProfile::create([
            'donor_id' => $this->donor->id,
            'recent_donation' => true,
            'last_donation_date' => Carbon::now()->subDays(10),
            'weight' => 70,
            'height' => 170,
        ]);

        $this->assertFalse($profile->is_eligible);
        $this->assertNotNull($profile->last_donation_date);

        // Act: Toggle off
        $profile->update([
            'recent_donation' => false,
        ]);

        $this->assertTrue($profile->is_eligible);
        $this->assertNull($profile->last_donation_date);
    }

    public function test_clearing_surgery_flag_removes_date_and_restores_eligibility()
    {
        $profile = DonorHealthProfile::create([
            'donor_id' => $this->donor->id,
            'has_recent_surgery' => true,
            'surgery_date' => Carbon::now()->subDays(5),
            'weight' => 70,
            'height' => 170,
        ]);

        $this->assertFalse($profile->is_eligible);
        $this->assertNotNull($profile->surgery_date);

        // Act: Toggle off
        $profile->update([
            'has_recent_surgery' => false,
        ]);

        $this->assertTrue($profile->is_eligible);
        $this->assertNull($profile->surgery_date);
    }
}
