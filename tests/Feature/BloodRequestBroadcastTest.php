<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\DonorHealthProfile;
use App\Models\User;
use App\Models\Organization;
use App\Models\Governorate;
use App\Enums\BloodType;
use App\Enums\BloodRequestStatus;
use App\Enums\UrgencyLevel;
use App\Enums\UserRole;
use App\Services\BloodRequestBroadcastService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloodRequestBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_broadcasts_to_eligible_donors_within_radius()
    {
        try {
            // 1. Setup Data
            $governorate = Governorate::firstOrCreate(['name' => 'غزة']);

            $organization = Organization::factory()->create([
                'governorate_id' => $governorate->id,
            ]);

            // Create an eligible donor within 10km ( Gaza center is approx 31.5, 34.45)
            $user1 = User::factory()->create(['role' => UserRole::DONOR]);
            $donor1 = Donor::factory()->create([
                'user_id' => $user1->id,
                'lat' => 31.500000,
                'lng' => 34.450000,
                'governorate_id' => $governorate->id,
            ]);
            DonorHealthProfile::factory()->create([
                'donor_id' => $donor1->id,
                'blood_type' => BloodType::O_POSITIVE,
                'is_eligible' => true,
            ]);

            // Create an ineligible donor (wrong blood type)
            $user2 = User::factory()->create(['role' => UserRole::DONOR]);
            $donor2 = Donor::factory()->create([
                'user_id' => $user2->id,
                'lat' => 31.500000,
                'lng' => 34.450000,
                'governorate_id' => $governorate->id,
            ]);
            DonorHealthProfile::factory()->create([
                'donor_id' => $donor2->id,
                'blood_type' => BloodType::AB_NEGATIVE,
                'is_eligible' => true,
            ]);

            // Create a donor outside radius (approx 50km away)
            $user3 = User::factory()->create(['role' => UserRole::DONOR]);
            $donor3 = Donor::factory()->create([
                'user_id' => $user3->id,
                'lat' => 31.900000, // Roughly 45km North
                'lng' => 34.450000,
                'governorate_id' => $governorate->id,
            ]);
            DonorHealthProfile::factory()->create([
                'donor_id' => $donor3->id,
                'blood_type' => BloodType::O_POSITIVE,
                'is_eligible' => true,
            ]);

            // 2. Create Blood Request (O+ request)
            $bloodRequest = BloodRequest::create([
                'organization_id' => $organization->id,
                'blood_type' => BloodType::O_POSITIVE,
                'units_needed' => 5,
                'urgency_level' => UrgencyLevel::MEDIUM,
                'search_radius_km' => 20,
                'lat' => 31.500000,
                'lng' => 34.450000,
                'status' => BloodRequestStatus::PENDING,
            ]);

            // 3. Execute Broadcast
            $count = $bloodRequest->broadcastToEligibleDonors();

            // 4. Verify
            // Only donor1 should match (O+, within 20km)
            $this->assertEquals(1, $count);
            $this->assertEquals(BloodRequestStatus::BROADCASTED, $bloodRequest->fresh()->status);
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
            throw $e;
        }
    }
}
