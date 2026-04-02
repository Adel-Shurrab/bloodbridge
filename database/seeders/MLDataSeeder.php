<?php

namespace Database\Seeders;

use App\Enums\BloodRequestStatus;
use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Organization;
use App\Models\RequestResponse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MLDataSeeder extends Seeder
{
    private const MIN_RESPONSES = 8;

    private const VETERAN = [           // ≥ 6 total donations
        [RequestResponseStatus::COMPLETED,    70],
        [RequestResponseStatus::NO_SHOW,      10],
        [RequestResponseStatus::DECLINED,     10],
        [RequestResponseStatus::IGNORED,       5],
        [RequestResponseStatus::UNREACHABLE,   5],
    ];

    private const REGULAR = [           // 3-5 total donations
        [RequestResponseStatus::COMPLETED,    50],
        [RequestResponseStatus::NO_SHOW,      15],
        [RequestResponseStatus::DECLINED,     12],
        [RequestResponseStatus::IGNORED,      13],
        [RequestResponseStatus::UNREACHABLE,  10],
    ];

    private const NEW_DONOR = [         // 1-2 total donations
        [RequestResponseStatus::COMPLETED,    35],
        [RequestResponseStatus::NO_SHOW,      18],
        [RequestResponseStatus::DECLINED,     12],
        [RequestResponseStatus::IGNORED,      20],
        [RequestResponseStatus::UNREACHABLE,  15],
    ];

    private const COLD_START = [        // 0 donations
        [RequestResponseStatus::COMPLETED,    25],
        [RequestResponseStatus::NO_SHOW,      15],
        [RequestResponseStatus::DECLINED,     10],
        [RequestResponseStatus::IGNORED,      30],
        [RequestResponseStatus::UNREACHABLE,  20],
    ];

    private array $requestSpecs = [
        ['al-shifa',   BloodType::AB_POSITIVE, UrgencyLevel::CRITICAL, 365, BloodRequestStatus::FULFILLED, 4, 'عملية زراعة كبد طارئة — AB+ مطلوب فوراً'],
        ['nasser',     BloodType::AB_POSITIVE, UrgencyLevel::NORMAL,   345, BloodRequestStatus::EXPIRED,   3, 'مريض يحتاج نقل دم دوري'],
        ['indonesian', BloodType::AB_POSITIVE, UrgencyLevel::CRITICAL, 315, BloodRequestStatus::FULFILLED, 5, 'حادث سير متعدد الضحايا — AB+ عاجل'],
        ['al-quds',    BloodType::AB_POSITIVE, UrgencyLevel::NORMAL,   285, BloodRequestStatus::FULFILLED, 2, 'مريض سرطان يخضع للعلاج الكيميائي'],
        ['alaqsa',     BloodType::AB_POSITIVE, UrgencyLevel::CRITICAL, 250, BloodRequestStatus::FULFILLED, 6, 'عملية قلب مفتوح — احتياطي AB+ طارئ'],
        ['european',   BloodType::AB_POSITIVE, UrgencyLevel::NORMAL,   215, BloodRequestStatus::FULFILLED, 3, 'تجديد مخزون بنك الدم من AB+'],
        ['najjar',     BloodType::AB_POSITIVE, UrgencyLevel::CRITICAL, 180, BloodRequestStatus::FULFILLED, 4, 'مصاب حرق جسيم بحاجة لعمليات نقل دم متعددة'],
        ['al-shifa',   BloodType::AB_POSITIVE, UrgencyLevel::NORMAL,   150, BloodRequestStatus::FULFILLED, 2, 'طفل مصاب بسرطان الدم — AB+ شهري'],
        ['nasser',     BloodType::AB_POSITIVE, UrgencyLevel::CRITICAL, 115, BloodRequestStatus::FULFILLED, 5, 'انهيار مبنى — AB+ للمصابين في العناية المركزة'],
        ['indonesian', BloodType::AB_POSITIVE, UrgencyLevel::NORMAL,    75, BloodRequestStatus::FULFILLED, 3, 'مريض قصور كلوي مزمن — AB+ دوري'],

        ['al-shifa',   BloodType::O_POSITIVE, UrgencyLevel::CRITICAL, 358, BloodRequestStatus::FULFILLED, 8, 'انهيار مبنى — O+ للمصابين البالغين'],
        ['nasser',     BloodType::O_POSITIVE, UrgencyLevel::NORMAL,   328, BloodRequestStatus::EXPIRED,   3, 'مريض أنيميا حادة يحتاج O+ بشكل دوري'],
        ['indonesian', BloodType::O_POSITIVE, UrgencyLevel::CRITICAL, 298, BloodRequestStatus::FULFILLED, 5, 'ولادة مع نزيف حاد — O+ عاجل'],
        ['al-quds',    BloodType::O_POSITIVE, UrgencyLevel::NORMAL,   258, BloodRequestStatus::FULFILLED, 2, 'مخزون بنك الدم منخفض — نداء لـ O+'],
        ['alaqsa',     BloodType::O_POSITIVE, UrgencyLevel::CRITICAL, 188, BloodRequestStatus::FULFILLED, 6, 'انفجار — O+ للضحايا الحرجين'],

        ['al-shifa',   BloodType::A_POSITIVE, UrgencyLevel::NORMAL,   348, BloodRequestStatus::FULFILLED, 3, 'مريض تلاسيميا — A+ شهري'],
        ['nasser',     BloodType::A_POSITIVE, UrgencyLevel::CRITICAL, 303, BloodRequestStatus::FULFILLED, 5, 'جراحة طارئة — A+ فوراً'],
        ['indonesian', BloodType::A_POSITIVE, UrgencyLevel::NORMAL,   223, BloodRequestStatus::EXPIRED,   2, 'تجهيز احتياطي A+ لبنك الدم'],
        ['al-quds',    BloodType::A_POSITIVE, UrgencyLevel::CRITICAL, 148, BloodRequestStatus::FULFILLED, 4, 'مريض في العناية المركزة — A+ عاجل'],

        ['al-shifa',   BloodType::B_POSITIVE, UrgencyLevel::NORMAL,   333, BloodRequestStatus::EXPIRED,   3, 'مريض غسيل كلى — B+ أسبوعي'],
        ['nasser',     BloodType::B_POSITIVE, UrgencyLevel::CRITICAL, 268, BloodRequestStatus::FULFILLED, 5, 'جراحة طارئة للبطن — B+ مطلوب'],
        ['alaqsa',     BloodType::B_POSITIVE, UrgencyLevel::NORMAL,   198, BloodRequestStatus::FULFILLED, 2, 'طفل مصاب يحتاج B+'],
        ['najjar',     BloodType::B_POSITIVE, UrgencyLevel::CRITICAL, 128, BloodRequestStatus::FULFILLED, 4, 'ضحية حادث — B+ نادر في المنطقة'],

        ['european',   BloodType::A_NEGATIVE, UrgencyLevel::CRITICAL, 360, BloodRequestStatus::FULFILLED, 3, 'حامل تحتاج A- قبل الولادة'],
        ['al-shifa',   BloodType::A_NEGATIVE, UrgencyLevel::NORMAL,   230, BloodRequestStatus::EXPIRED,   2, 'A- شحيح في بنك الدم'],
        ['nasser',     BloodType::A_NEGATIVE, UrgencyLevel::CRITICAL, 108, BloodRequestStatus::FULFILLED, 4, 'حادث سير — A- طارئاً'],

        ['indonesian', BloodType::B_NEGATIVE, UrgencyLevel::CRITICAL, 350, BloodRequestStatus::FULFILLED, 2, 'رضيع يحتاج B- نادر'],
        ['al-quds',    BloodType::B_NEGATIVE, UrgencyLevel::NORMAL,   220, BloodRequestStatus::EXPIRED,   1, 'تجميع مخزون B-'],
        ['alaqsa',     BloodType::B_NEGATIVE, UrgencyLevel::CRITICAL,  88, BloodRequestStatus::FULFILLED, 3, 'زراعة كلى — B- طارئ'],

        ['al-shifa',   BloodType::O_NEGATIVE, UrgencyLevel::CRITICAL, 385, BloodRequestStatus::FULFILLED, 5, 'مجهول الهوية في الطوارئ — O- الوحيد الآمن'],
        ['nasser',     BloodType::O_NEGATIVE, UrgencyLevel::NORMAL,   158, BloodRequestStatus::FULFILLED, 3, 'طفل مصاب — O- ضروري'],

        ['european',   BloodType::AB_NEGATIVE, UrgencyLevel::CRITICAL, 366, BloodRequestStatus::FULFILLED, 2, 'زراعة قلب — AB- نادر جداً'],
        ['najjar',     BloodType::AB_NEGATIVE, UrgencyLevel::NORMAL,   175, BloodRequestStatus::EXPIRED,   1, 'احتياطي AB- لحالات الطوارئ'],
    ];

    // Permanently ineligible donors — skip entirely
    private array $skipNationalIds = ['410234567', '420456789'];

    // Arabic medical decline reasons
    private array $declineReasons = [
        'استبعاد طبي: انخفاض نسبة الهيموجلوبين',
        'استبعاد طبي: ضغط دم مرتفع — 160/100 mmHg',
        'استبعاد طبي: وزن دون الحد المطلوب',
        'استبعاد طبي: علامات عدوى حادة',
        'استبعاد طبي: أدوية مضادة للتخثر',
        'استبعاد طبي: تبرع حديث منذ أقل من 90 يوماً',
    ];

    public function run(): void
    {
        $orgs   = Organization::all()->keyBy('slug');
        $donors = Donor::with('healthProfile')
            ->whereHas('user')
            ->get()
            ->filter(fn($d) => ! in_array($d->national_id, $this->skipNationalIds))
            ->values();

        foreach ($this->requestSpecs as [$slug, $bloodType, $urgency, $daysAgo, $reqStatus, $units, $note]) {
            $org = $orgs[$slug] ?? null;
            if (! $org) continue;

            $broadcastedAt = Carbon::now()
                ->subDays($daysAgo)
                ->setHour(rand(7, 20))
                ->setMinute(rand(0, 59))
                ->setSecond(0);

            $fulfilledAt = $reqStatus === BloodRequestStatus::FULFILLED
                ? $broadcastedAt->copy()->addHours(rand(3, 20))
                : null;

            $request = BloodRequest::create([
                'organization_id'          => $org->id,
                'blood_type'               => $bloodType,
                'units_needed'             => $units,
                'urgency_level'            => $urgency,
                'additional_notes'         => $note,
                'search_radius_km'         => 20,
                'actual_search_radius_km'  => 25,
                'lat'                      => $org->lat,
                'lng'                      => $org->lng,
                'location_address'         => 'بنك الدم',
                'status'                   => $reqStatus,
                'broadcasted_at'           => $broadcastedAt,
                'fulfilled_at'             => $fulfilledAt,
                'created_at'               => $broadcastedAt,
                'updated_at'               => $fulfilledAt ?? $broadcastedAt,
            ]);

            $compatibleDonors = $this->filterCompatible($donors, $bloodType);

            // Critical broadcasts get higher donor participation
            $participationRate = $urgency === UrgencyLevel::CRITICAL ? 0.80 : 0.65;

            foreach ($compatibleDonors as $donor) {
                if ((rand(1, 100) / 100) > $participationRate) {
                    continue;
                }

                $this->createResponse($request, $donor, $broadcastedAt);
            }
        }

        $historicalRequests = BloodRequest::whereIn('status', [
            BloodRequestStatus::FULFILLED,
            BloodRequestStatus::EXPIRED,
        ])
            ->whereNotNull('broadcasted_at')
            ->whereDate('broadcasted_at', '<', Carbon::now()->subDays(14))
            ->get();

        foreach ($donors as $donor) {
            $existing = RequestResponse::where('donor_id', $donor->id)->count();
            if ($existing >= self::MIN_RESPONSES) {
                continue;
            }

            $needed    = self::MIN_RESPONSES - $existing;
            $bloodType = $donor->healthProfile?->blood_type;

            if (! $bloodType) {
                continue;
            }

            $eligible = $historicalRequests->filter(function ($req) use ($donor, $bloodType) {
                $compatible = in_array($bloodType, $req->blood_type->getCompatibleDonorTypes(), true);
                $notExists  = ! RequestResponse::where('blood_request_id', $req->id)
                    ->where('donor_id', $donor->id)
                    ->exists();
                return $compatible && $notExists;
            })->take($needed + 5);

            foreach ($eligible->take($needed) as $req) {
                $broadcastedAt = Carbon::parse($req->broadcasted_at);
                $this->createResponse($req, $donor, $broadcastedAt);
            }
        }
    }

    private function filterCompatible($donors, BloodType $requestType): array
    {
        $compatible = $requestType->getCompatibleDonorTypes();

        return $donors->filter(function ($donor) use ($compatible) {
            $type = $donor->healthProfile?->blood_type;
            return $type && in_array($type, $compatible, true);
        })->values()->all();
    }

    private function createResponse(BloodRequest $request, Donor $donor, Carbon $broadcastedAt): void
    {
        $exists = RequestResponse::where('blood_request_id', $request->id)
            ->where('donor_id', $donor->id)
            ->exists();

        if ($exists) {
            return;
        }

        $totalDonations = $donor->healthProfile?->total_donations ?? 0;
        $status         = $this->pickStatus($totalDonations, $request->urgency_level);
        $respondedAt    = $broadcastedAt->copy()->addMinutes(rand(5, 180));

        $qrCode    = null;
        $qrExpiry  = null;
        $verifiedAt = null;
        $decline   = null;

        if ($status === RequestResponseStatus::COMPLETED || $status === RequestResponseStatus::DECLINED) {
            $qrCode    = Str::random(32);
            $qrExpiry  = $respondedAt->copy()->addDays(7);
            $verifiedAt = $respondedAt->copy()->addHours(rand(1, 4));
        }

        if ($status === RequestResponseStatus::DECLINED) {
            $decline = $this->declineReasons[array_rand($this->declineReasons)];
        }

        RequestResponse::create([
            'blood_request_id'     => $request->id,
            'donor_id'             => $donor->id,
            'status'               => $status,
            'responded_at'         => $respondedAt,
            'verified_at'          => $verifiedAt,
            'verification_qr_code' => $qrCode,
            'qr_code_expires_at'   => $qrExpiry,
            'decline_reason'       => $decline,
            'created_at'           => $respondedAt,
            'updated_at'           => $respondedAt,
        ]);
    }

    private function pickStatus(int $totalDonations, UrgencyLevel $urgency): RequestResponseStatus
    {
        $pool = match (true) {
            $totalDonations >= 6 => self::VETERAN,
            $totalDonations >= 3 => self::REGULAR,
            $totalDonations >= 1 => self::NEW_DONOR,
            default              => self::COLD_START,
        };

        // Critical urgency gives a small boost to completion rate
        if ($urgency === UrgencyLevel::CRITICAL) {
            $pool = array_map(
                fn($e) => [$e[0], $e[0] === RequestResponseStatus::COMPLETED ? (int) ceil($e[1] * 1.15) : $e[1]],
                $pool
            );
        }

        $total = (int) array_sum(array_column($pool, 1));
        $roll  = rand(1, $total);
        $cum   = 0;

        foreach ($pool as [$status, $weight]) {
            $cum += $weight;
            if ($roll <= $cum) {
                return $status;
            }
        }

        return RequestResponseStatus::COMPLETED;
    }
}
