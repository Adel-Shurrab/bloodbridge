<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Helpers\GeoHelper;
use App\Models\Governorate;
use App\Enums\BloodType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestMatchingController extends Controller
{
    /**
     * Test donor matching algorithm
     */
    public function testMatching(Request $request)
    {
        // Get parameters from request or use defaults
        $lat = $request->filled('lat') ? (float)$request->input('lat') : 31.5313; // Gaza City default
        $lng = $request->filled('lng') ? (float)$request->input('lng') : 34.4661;
        $radiusKm = $request->input('radius', 10);
        $governorateId = $request->input('governorate_id');
        $bloodTypeInput = $request->input('blood_type');

        // Resolve blood type from input (supports both ID and Enum name)
        $bloodTypeEnum = null;
        if (is_numeric($bloodTypeInput)) {
            $bloodTypeEnum = BloodType::tryFrom((int)$bloodTypeInput);
        } elseif (is_string($bloodTypeInput)) {
            foreach (BloodType::cases() as $case) {
                if ($case->name === $bloodTypeInput) {
                    $bloodTypeEnum = $case;
                    break;
                }
            }
        }

        // Default to O_POSITIVE if not found
        if (!$bloodTypeEnum) {
            $bloodTypeEnum = BloodType::O_POSITIVE;
        }

        // Pass the integer value to the view so the select element works correctly
        $bloodType = $bloodTypeEnum->value;

        // Get compatible blood types
        $compatibleTypes = $bloodTypeEnum->getCompatibleDonorTypes();

        // Step 1: Find donors within radius with governorate fallback
        $donorsInRadius = Donor::withinRadius($lat, $lng, $radiusKm, $governorateId)
            ->with(['healthProfile', 'governorate', 'user'])
            ->get();

        // Step 2: Filter by blood type compatibility
        $compatibleDonors = $donorsInRadius->filter(function ($donor) use ($compatibleTypes) {
            if (!$donor->healthProfile) {
                return false;
            }

            $verifiedType = $donor->healthProfile->verified_blood_type;
            $declaredType = $donor->healthProfile->blood_type;

            // Check verified type first, fallback to declared type
            $donorBloodType = $verifiedType ?? $declaredType;

            return in_array($donorBloodType, $compatibleTypes);
        });

        // Step 3: Filter by eligibility
        $eligibleDonors = $compatibleDonors->filter(function ($donor) {
            if (!$donor->healthProfile) {
                return false;
            }

            // Must be eligible in health profile
            if (!$donor->healthProfile->is_eligible) {
                return false;
            }

            // Defense in depth: also check next_eligible_date explicitly
            if ($donor->healthProfile->next_eligible_date) {
                $nextEligibleDate = \Carbon\Carbon::parse($donor->healthProfile->next_eligible_date)->startOfDay();
                if ($nextEligibleDate->isFuture()) {
                    return false; // Still in cooldown period
                }
            }

            // Check for permanent exclusions
            $hasPermanentExclusion = $donor->eligibilityLogs()
                ->where('is_eligible', false)
                ->where('is_permanent', true)
                ->exists();

            if ($hasPermanentExclusion) {
                return false;
            }

            // Time-based notification de-duplication: check if notified within last 2 hours
            $recentlyNotified = $donor->responses()
                ->where('created_at', '>=', \Carbon\Carbon::now()->subHours(2))
                ->whereIn('status', [
                    \App\Enums\RequestResponseStatus::PENDING,
                    \App\Enums\RequestResponseStatus::ACCEPTED
                ])
                ->exists();

            return !$recentlyNotified;
        });

        // Calculate distances for all donors
        $donorsWithDistance = $donorsInRadius->map(function ($donor) use ($lat, $lng, $compatibleDonors, $eligibleDonors) {
            $distance = isset($donor->lat, $donor->lng)
                ? GeoHelper::calculateDistance($lat, $lng, $donor->lat, $donor->lng)
                : null;

            return [
                'id' => $donor->id,
                'name' => $donor->user?->name ?? 'N/A',
                'phone' => $donor->user?->phone ?? 'N/A',
                'email' => $donor->user?->email ?? 'N/A',
                'national_id' => $donor->national_id ?? 'N/A',
                'gender' => $donor->gender?->getLabel() ?? 'N/A',
                'blood_type' => $donor->healthProfile?->verified_blood_type?->getLabel()
                    ?? $donor->healthProfile?->blood_type?->getLabel()
                    ?? 'N/A',
                'governorate' => $donor->governorate?->name ?? 'N/A',
                'distance_km' => $distance ? round($distance, 2) : null,
                'is_eligible' => $donor->healthProfile?->is_eligible ?? false,
                'has_permanent_exclusion' => $donor->eligibilityLogs()
                    ->where('is_eligible', false)
                    ->where('is_permanent', true)
                    ->exists(),
                'is_compatible' => $compatibleDonors->contains($donor),
                'is_final_match' => $eligibleDonors->contains($donor),
            ];
        })->sortBy('distance_km')->values();

        // Prepare response
        return view('test-matching', [
            'searchLat' => $lat,
            'searchLng' => $lng,
            'radiusKm' => $radiusKm,
            'bloodType' => $bloodType,
            'bloodTypeLabel' => $bloodTypeEnum->getLabel(),
            'compatibleTypes' => collect($compatibleTypes)->map(fn($t) => $t->getLabel())->toArray(),
            'totalDonorsInRadius' => $donorsInRadius->count(),
            'compatibleDonorsCount' => $compatibleDonors->count(),
            'eligibleDonorsCount' => $eligibleDonors->count(),
            'donors' => $donorsWithDistance,
            'allBloodTypes' => BloodType::cases(),
            'allGovernorates' => Governorate::all(),
            'selectedGovernorateId' => $governorateId,
        ]);
    }

    /**
     * API endpoint for testing
     */
    public function testMatchingApi(Request $request)
    {
        $lat = $request->filled('lat') ? (float)$request->input('lat') : 31.5313;
        $lng = $request->filled('lng') ? (float)$request->input('lng') : 34.4661;
        $radiusKm = $request->input('radius', 10);
        $governorateId = $request->input('governorate_id');
        $bloodTypeInput = $request->input('blood_type');

        // Resolve blood type from input (supports both ID and Enum name)
        $bloodTypeEnum = null;
        if (is_numeric($bloodTypeInput)) {
            $bloodTypeEnum = BloodType::tryFrom((int)$bloodTypeInput);
        } elseif (is_string($bloodTypeInput)) {
            foreach (BloodType::cases() as $case) {
                if ($case->name === $bloodTypeInput) {
                    $bloodTypeEnum = $case;
                    break;
                }
            }
        }

        if (!$bloodTypeEnum) {
            $bloodTypeEnum = BloodType::O_POSITIVE;
        }

        $bloodType = $bloodTypeEnum->value;
        $compatibleTypes = $bloodTypeEnum->getCompatibleDonorTypes();

        $donorsInRadius = Donor::withinRadius($lat, $lng, $radiusKm, $governorateId)
            ->with(['healthProfile', 'governorate', 'user'])
            ->get();

        $results = $donorsInRadius->map(function ($donor) use ($lat, $lng, $compatibleTypes) {
            $distance = isset($donor->lat, $donor->lng)
                ? GeoHelper::calculateDistance($lat, $lng, $donor->lat, $donor->lng)
                : null;

            $donorBloodType = $donor->healthProfile?->verified_blood_type
                ?? $donor->healthProfile?->blood_type;

            $isCompatible = $donorBloodType && in_array($donorBloodType, $compatibleTypes);
            $isEligible = $donor->healthProfile?->is_eligible ?? false;
            $hasPermanentExclusion = $donor->eligibilityLogs()
                ->where('is_eligible', false)
                ->where('is_permanent', true)
                ->exists();

            return [
                'id' => $donor->id,
                'name' => $donor->user?->name ?? 'N/A',
                'phone' => $donor->user?->phone ?? 'N/A',
                'email' => $donor->user?->email ?? 'N/A',
                'national_id' => $donor->national_id ?? 'N/A',
                'gender' => $donor->gender?->getLabel() ?? 'N/A',
                'blood_type' => $donorBloodType?->getLabel() ?? 'N/A',
                'governorate' => $donor->governorate?->name ?? 'N/A',
                'distance_km' => $distance ? round($distance, 2) : null,
                'is_compatible' => $isCompatible,
                'is_eligible' => $isEligible,
                'has_permanent_exclusion' => $hasPermanentExclusion,
                'is_final_match' => $isCompatible && $isEligible && !$hasPermanentExclusion,
            ];
        })->sortBy('distance_km')->values();

        return response()->json([
            'search_params' => [
                'lat' => $lat,
                'lng' => $lng,
                'radius_km' => $radiusKm,
                'governorate_id' => $governorateId,
                'blood_type_needed' => $bloodType,
                'compatible_donor_types' => collect($compatibleTypes)->map(fn($t) => $t->value)->toArray(),
            ],
            'results' => [
                'total_in_radius' => $donorsInRadius->count(),
                'blood_compatible' => $results->where('is_compatible', true)->count(),
                'eligible' => $results->where('is_eligible', true)->count(),
                'final_matches' => $results->where('is_final_match', true)->count(),
            ],
            'donors' => $results,
        ]);
    }
}
