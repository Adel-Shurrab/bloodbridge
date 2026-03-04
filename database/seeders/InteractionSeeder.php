<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use App\Enums\BloodRequestStatus;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        $requests = BloodRequest::all()->keyBy(function ($r) {
            return $r->organization->slug . '_' . $r->blood_type->value . '_' . $r->status->value;
        });

        $req1 = BloodRequest::whereHas('organization', fn($q) => $q->where('slug', 'al-shifa'))
            ->where('blood_type', \App\Enums\BloodType::O_POSITIVE)
            ->where('status', BloodRequestStatus::BROADCASTED)
            ->first();

        $req2 = BloodRequest::whereHas('organization', fn($q) => $q->where('slug', 'nasser'))
            ->where('blood_type', \App\Enums\BloodType::A_NEGATIVE)
            ->first();

        $req3 = BloodRequest::whereHas('organization', fn($q) => $q->where('slug', 'indonesian'))
            ->where('blood_type', \App\Enums\BloodType::B_POSITIVE)
            ->first();

        $req4 = BloodRequest::whereHas('organization', fn($q) => $q->where('slug', 'al-quds'))
            ->where('blood_type', \App\Enums\BloodType::AB_POSITIVE)
            ->first();

        $req5 = BloodRequest::whereHas('organization', fn($q) => $q->where('slug', 'alaqsa'))
            ->where('blood_type', \App\Enums\BloodType::O_NEGATIVE)
            ->first();

        $req8 = BloodRequest::whereHas('organization', fn($q) => $q->where('slug', 'al-shifa'))
            ->where('blood_type', \App\Enums\BloodType::AB_NEGATIVE)
            ->first();

        $d = fn(string $id) => Donor::whereHas('user')->where('national_id', $id)->first();

        /**
         * ══════════════════════════════════════════════════════════════════════
         * REQUEST 1 — الشفاء, O+, BROADCASTED (active critical emergency)
         * ══════════════════════════════════════════════════════════════════════
         * 5 responses covering: PENDING, ACCEPTED, PENDING, IGNORED, PENDING
         */
        if ($req1) {
            $broadcastedAt = Carbon::parse($req1->broadcasted_at);

            $this->respond(
                $req1,
                $d('400123456'),
                RequestResponseStatus::PENDING,
                $broadcastedAt->copy()->addMinutes(8)
            );

            $this->respond(
                $req1,
                $d('400567890'),
                RequestResponseStatus::ACCEPTED,
                $broadcastedAt->copy()->addMinutes(15),
                qrCode: true
            );

            $this->respond(
                $req1,
                $d('440123456'),
                RequestResponseStatus::PENDING,
                $broadcastedAt->copy()->addMinutes(22)
            );

            $this->respond(
                $req1,
                $d('410345678'),
                RequestResponseStatus::IGNORED,
                $broadcastedAt->copy()->addMinutes(12),
                declineReason: 'ظرف طارئ، انتهت إمكانية المجيء'
            );

            $this->respond(
                $req1,
                $d('420123456'),
                RequestResponseStatus::IGNORED,
                $broadcastedAt->copy()->addMinutes(35),
                declineReason: 'بعيد عن الموقع حالياً'
            );
        }

        /**
         * ══════════════════════════════════════════════════════════════════════
         * REQUEST 2 — ناصر, A-, BROADCASTED (normal, 2 hours ago)
         * ══════════════════════════════════════════════════════════════════════
         * 4 responses: PENDING, ACCEPTED, DECLINED (medical), NO_SHOW
         */
        if ($req2) {
            $broadcastedAt = Carbon::parse($req2->broadcasted_at);

            $this->respond(
                $req2,
                $d('400234567'),
                RequestResponseStatus::PENDING,
                $broadcastedAt->copy()->addMinutes(20)
            );

            $this->respond(
                $req2,
                $d('410123456'),
                RequestResponseStatus::NO_SHOW,
                $broadcastedAt->copy()->addMinutes(10)
            );

            $this->respond(
                $req2,
                $d('420234567'),
                RequestResponseStatus::ACCEPTED,
                $broadcastedAt->copy()->addMinutes(45),
                qrCode: true
            );

            $this->respond(
                $req2,
                $d('430567890'),
                RequestResponseStatus::DECLINED,
                $broadcastedAt->copy()->addMinutes(60),
                verifiedAt: $broadcastedAt->copy()->addMinutes(90),
                declineReason: 'استبعاد طبي: نقص حاد في الهيموجلوبين — الهيموجلوبين 10.2 g/dL'
            );
        }

        /**
         * ══════════════════════════════════════════════════════════════════════
         * REQUEST 3 — الإندونيسي, B+, FULFILLED (completed 4 days ago)
         * ══════════════════════════════════════════════════════════════════════
         * 5 responses: 3 COMPLETED, 1 DECLINED (medical), 1 NO_SHOW
         */
        if ($req3) {
            $broadcastedAt = Carbon::parse($req3->broadcasted_at);

            $this->respond(
                $req3,
                $d('410567890'),
                RequestResponseStatus::COMPLETED,
                $broadcastedAt->copy()->addMinutes(30),
                verifiedAt: $broadcastedAt->copy()->addHours(2),
                qrCode: true
            );

            $this->respond(
                $req3,
                $d('430234567'),
                RequestResponseStatus::COMPLETED,
                $broadcastedAt->copy()->addMinutes(55),
                verifiedAt: $broadcastedAt->copy()->addHours(3),
                qrCode: true
            );

            $this->respond(
                $req3,
                $d('440234567'),
                RequestResponseStatus::COMPLETED,
                $broadcastedAt->copy()->addMinutes(70),
                verifiedAt: $broadcastedAt->copy()->addHours(4),
                qrCode: true
            );

            $this->respond(
                $req3,
                $d('420345678'),
                RequestResponseStatus::DECLINED,
                $broadcastedAt->copy()->addMinutes(40),
                verifiedAt: $broadcastedAt->copy()->addMinutes(80),
                declineReason: 'استبعاد طبي: ضغط دم مرتفع — 160/100 mmHg'
            );

            $this->respond(
                $req3,
                $d('430345678'),
                RequestResponseStatus::NO_SHOW,
                $broadcastedAt->copy()->addMinutes(25)
            );
        }

        /**
         * ══════════════════════════════════════════════════════════════════════
         * REQUEST 4 — القدس, AB+, MATCHED (donors accepted, waiting)
         * ══════════════════════════════════════════════════════════════════════
         * 3 responses: 2 ACCEPTED (at hospital), 1 PENDING (on the way)
         */
        if ($req4) {
            $broadcastedAt = Carbon::parse($req4->broadcasted_at);

            $this->respond(
                $req4,
                $d('400456789'),
                RequestResponseStatus::ACCEPTED,
                $broadcastedAt->copy()->addMinutes(20),
                qrCode: true
            );

            $this->respond(
                $req4,
                $d('420345678'),
                RequestResponseStatus::ACCEPTED,
                $broadcastedAt->copy()->addMinutes(35),
                qrCode: true
            );

            $this->respond(
                $req4,
                $d('430456789'),
                RequestResponseStatus::PENDING,
                $broadcastedAt->copy()->addMinutes(50)
            );
        }

        /**
         * ══════════════════════════════════════════════════════════════════════
         * REQUEST 5 — الأقصى, O-, BROADCASTED (critical, rare blood type)
         * ══════════════════════════════════════════════════════════════════════
         * 3 responses: PENDING, ACCEPTED, UNREACHABLE
         */
        if ($req5) {
            $broadcastedAt = Carbon::parse($req5->broadcasted_at);

            $this->respond(
                $req5,
                $d('400567890'),
                RequestResponseStatus::PENDING,
                $broadcastedAt->copy()->addMinutes(5)
            );

            $this->respond(
                $req5,
                $d('420567890'),
                RequestResponseStatus::ACCEPTED,
                $broadcastedAt->copy()->addMinutes(18),
                qrCode: true
            );

            $this->respond(
                $req5,
                $d('440456789'),
                RequestResponseStatus::UNREACHABLE,
                $broadcastedAt->copy()->addMinutes(3)
            );
        }

        /**
         * ══════════════════════════════════════════════════════════════════════
         * REQUEST 8 — الشفاء, AB-, EXPIRED (old — nobody showed up)
         * ══════════════════════════════════════════════════════════════════════
         * 2 responses: IGNORED, UNREACHABLE
         */
        if ($req8) {
            $broadcastedAt = Carbon::parse($req8->broadcasted_at);

            $this->respond(
                $req8,
                $d('430456789'),
                RequestResponseStatus::IGNORED,
                $broadcastedAt->copy()->addHours(1),
                declineReason: 'لم أتمكن من الوصول وقتها'
            );

            $this->respond(
                $req8,
                $d('440567890'),
                RequestResponseStatus::UNREACHABLE,
                $broadcastedAt->copy()->addHours(3)
            );
        }
    }

    /**
     * Helper to create a response record with a QR code when needed.
     */
    private function respond(
        ?BloodRequest $request,
        ?Donor $donor,
        RequestResponseStatus $status,
        Carbon $respondedAt,
        ?Carbon $verifiedAt = null,
        ?string $declineReason = null,
        bool $qrCode = false,
    ): void {
        if (! $request || ! $donor) return;

        $exists = RequestResponse::where('blood_request_id', $request->id)
            ->where('donor_id', $donor->id)
            ->exists();

        if ($exists) return;

        $qrValue    = null;
        $qrExpiry   = null;

        if ($qrCode) {
            $qrValue  = Str::uuid()->toString();
            $qrExpiry = $respondedAt->copy()->addHours(24);
        }

        RequestResponse::create([
            'blood_request_id'    => $request->id,
            'donor_id'            => $donor->id,
            'status'              => $status,
            'responded_at'        => $respondedAt,
            'verified_at'         => $verifiedAt,
            'verification_qr_code' => $qrValue,
            'qr_code_expires_at'  => $qrExpiry,
            'decline_reason'      => $declineReason,
            'created_at'          => $respondedAt,
            'updated_at'          => $respondedAt,
        ]);
    }
}
