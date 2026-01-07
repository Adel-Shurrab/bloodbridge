<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DonorHealthProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Donor;
use App\Models\Governorate;

class DonorEligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_donor_is_ineligible_with_chronic_disease()
    {
        $profile = new DonorHealthProfile([
            'chronic_disease' => true,
        ]);

        $eligibility = $profile->calculateEligibility();

        $this->assertFalse($eligibility['is_eligible']);
        $this->assertNull($eligibility['next_eligible_date']);
    }

    public function test_donor_is_ineligible_with_recent_donation()
    {
        $lastDonationDate = Carbon::now()->subDays(30);
        $profile = new DonorHealthProfile([
            'last_donation_date' => $lastDonationDate,
        ]);

        $eligibility = $profile->calculateEligibility();

        $this->assertFalse($eligibility['is_eligible']);
        $this->assertEquals(
            $lastDonationDate->copy()->addDays(90)->startOfDay(),
            $eligibility['next_eligible_date']->startOfDay()
        );
    }

    public function test_donor_is_ineligible_with_recent_surgery()
    {
        $surgeryDate = Carbon::now()->subDays(10);
        $profile = new DonorHealthProfile([
            'surgery_date' => $surgeryDate,
        ]);

        $eligibility = $profile->calculateEligibility();

        $this->assertFalse($eligibility['is_eligible']);
        $this->assertEquals(
            $surgeryDate->copy()->addDays(28)->startOfDay(),
            $eligibility['next_eligible_date']->startOfDay()
        );
    }

    public function test_donor_is_ineligible_with_infection()
    {
        $profile = new DonorHealthProfile([
            'infection' => true,
        ]);

        $eligibility = $profile->calculateEligibility();

        $this->assertFalse($eligibility['is_eligible']);
        $this->assertEquals(
            Carbon::now()->addDays(14)->startOfDay(),
            $eligibility['next_eligible_date']->startOfDay()
        );
    }

    public function test_eligible_date_chooses_latest()
    {
        $lastDonationDate = Carbon::now()->subDays(80); // Eligible in 10 days
        $surgeryDate = Carbon::now()->subDays(5);     // Eligible in 23 days

        $profile = new DonorHealthProfile([
            'last_donation_date' => $lastDonationDate,
            'surgery_date' => $surgeryDate,
        ]);

        $eligibility = $profile->calculateEligibility();

        $this->assertFalse($eligibility['is_eligible']);
        $this->assertEquals(
            $surgeryDate->copy()->addDays(28)->startOfDay(),
            $eligibility['next_eligible_date']->startOfDay()
        );
    }

    public function test_model_hook_updates_eligibility_on_save()
    {
        $user = User::create([
            'name' => 'Test Donor',
            'email' => 'test@example.com',
            'phone' => '123456789',
            'password' => 'password',
            'role' => User::ROLE_DONOR,
        ]);

        $governorate = Governorate::create(['name' => 'Test Governorate']);

        $donor = Donor::create([
            'user_id' => $user->id,
            'governorate_id' => $governorate->id,
            'national_id' => '123456789',
            'gender' => Donor::GENDER_MALE,
            'birth_date' => '1990-01-01',
        ]);

        $profile = DonorHealthProfile::create([
            'donor_id' => $donor->id,
            'last_donation_date' => Carbon::now()->subDays(10),
            'recent_donation' => true,
        ]);

        $this->assertFalse($profile->is_eligible);
        $this->assertNotNull($profile->next_eligible_date);

        $profile->update([
            'last_donation_date' => Carbon::now()->subDays(100),
            'recent_donation' => false,
        ]);

        $this->assertTrue($profile->is_eligible);
        $this->assertNull($profile->next_eligible_date);
    }
}
