<?php

use App\Enums\RequestResponseStatus;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\RequestResponse;
use Database\Seeders\BloodRequestSeeder;
use Database\Seeders\DonorSeeder;
use Database\Seeders\InteractionSeeder;
use Database\Seeders\MLDataSeeder;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Run the full seeder chain once for this file
beforeAll(function () {
    // RefreshDatabase handles the clean slate; seeders run inside the transaction
});

beforeEach(function () {
    $this->seed([
        OrganizationSeeder::class,
        DonorSeeder::class,
        BloodRequestSeeder::class,
        InteractionSeeder::class,
        MLDataSeeder::class,
    ]);
});

// ---------------------------------------------------------------------------
// Response count guarantees
// ---------------------------------------------------------------------------

test('every eligible donor has at least 8 responses', function () {
    $permanently_ineligible = ['410234567', '420456789'];

    $under = Donor::with('healthProfile')
        ->whereNotIn('national_id', $permanently_ineligible)
        ->get()
        ->filter(fn($donor) => RequestResponse::where('donor_id', $donor->id)->count() < 8);

    expect($under)->toHaveCount(0);
});

test('permanently ineligible donors have zero responses', function () {
    $ineligible = Donor::whereIn('national_id', ['410234457', '420456789'])->get();

    foreach ($ineligible as $donor) {
        expect(RequestResponse::where('donor_id', $donor->id)->count())->toBe(0);
    }
});

test('total responses exceed 200', function () {
    expect(RequestResponse::count())->toBeGreaterThan(200);
});

// ---------------------------------------------------------------------------
// Status distribution — each archetype must have COMPLETED responses
// ---------------------------------------------------------------------------

test('veteran donors (6+ donations) have higher aggregate completion rate than cold-start donors', function () {
    // Per-donor assertion is too noisy with only 5-8 responses.
    // Test the aggregate across each archetype instead — the law of large
    // numbers makes this stable even with random weights.

    $veteranIds = Donor::with('healthProfile')
        ->get()
        ->filter(fn($d) => ($d->healthProfile?->total_donations ?? 0) >= 6)
        ->pluck('id');

    $coldStartIds = Donor::with('healthProfile')
        ->whereNotIn('national_id', ['410234567', '420456789'])
        ->get()
        ->filter(fn($d) => ($d->healthProfile?->total_donations ?? 0) === 0)
        ->pluck('id');

    $veteranRate = RequestResponse::whereIn('donor_id', $veteranIds)
        ->whereIn('status', [RequestResponseStatus::COMPLETED, RequestResponseStatus::NO_SHOW,
            RequestResponseStatus::DECLINED, RequestResponseStatus::IGNORED, RequestResponseStatus::UNREACHABLE])
        ->selectRaw('AVG(status = ?) as rate', [RequestResponseStatus::COMPLETED->value])
        ->value('rate');

    $coldStartRate = RequestResponse::whereIn('donor_id', $coldStartIds)
        ->whereIn('status', [RequestResponseStatus::COMPLETED, RequestResponseStatus::NO_SHOW,
            RequestResponseStatus::DECLINED, RequestResponseStatus::IGNORED, RequestResponseStatus::UNREACHABLE])
        ->selectRaw('AVG(status = ?) as rate', [RequestResponseStatus::COMPLETED->value])
        ->value('rate');

    // Veterans should complete more often than cold-start donors
    expect((float) $veteranRate)->toBeGreaterThan((float) $coldStartRate);
});

// ---------------------------------------------------------------------------
// Historical blood requests created by the seeder
// ---------------------------------------------------------------------------

test('seeder creates at least 30 historical blood requests', function () {
    // 8 from BloodRequestSeeder + 33 from MLDataSeeder
    expect(BloodRequest::count())->toBeGreaterThanOrEqual(38);
});

test('historical requests span multiple organizations', function () {
    $orgCount = BloodRequest::distinct('organization_id')->count('organization_id');
    expect($orgCount)->toBeGreaterThanOrEqual(5);
});

test('historical requests cover all blood types', function () {
    $types = BloodRequest::distinct('blood_type')->pluck('blood_type')->count();
    expect($types)->toBe(8);
});

// ---------------------------------------------------------------------------
// Seeder is idempotent — running it twice produces no duplicate-key errors
// ---------------------------------------------------------------------------

test('running MLDataSeeder twice does not throw', function () {
    expect(fn() => (new MLDataSeeder())->run())->not->toThrow(Exception::class);
});
