<?php

namespace App\Services;

use App\Enums\RequestResponseStatus;
use App\Enums\NotificationType;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\RequestResponse;
use App\Notifications\DonorResponseNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BloodRequestActionService
{
    public function __construct(
        protected QRCodeService $qrCodeService
    ) {}

    /**
     * Donor accepts a blood request.
     *
     * @throws RuntimeException if ineligible or request inactive
     */
    public function accept(Donor $donor, BloodRequest $request): RequestResponse
    {
        if (! $request->isActive()) {
            throw new RuntimeException('هذا الطلب غير متاح الآن');
        }

        $profile = $donor->healthProfile;
        $isEligible = $profile
            && $profile->is_eligible
            && (
                is_null($profile->next_eligible_date)
                || $profile->next_eligible_date->startOfDay()->isPast()
                || $profile->next_eligible_date->startOfDay()->isToday()
            );

        if (! $isEligible) {
            throw new RuntimeException('غير مؤهل للتبرع حاليًا');
        }

        $alreadyAccepted = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('blood_request_id', '!=', $request->id)
            ->whereIn('status', [
                RequestResponseStatus::PENDING->value,
                RequestResponseStatus::ACCEPTED->value,
            ])
            ->exists();

        if ($alreadyAccepted) {
            throw new RuntimeException('لديك طلب آخر مقبول بالفعل. يرجى التراجع عنه أولاً قبل قبول طلب جديد.');
        }

        $response = DB::transaction(function () use ($donor, $request) {
            $response = RequestResponse::query()->updateOrCreate(
                [
                    'donor_id' => $donor->id,
                    'blood_request_id' => $request->id,
                ],
                [
                    'status' => RequestResponseStatus::PENDING->value,
                    'responded_at' => now(),
                ]
            );

            $this->qrCodeService->generate($response, true);

            return $response;
        });

        $orgUser = $request->organization?->user;
        if ($orgUser) {
            $response->load(['donor.user', 'donor.healthProfile', 'bloodRequest.organization']);
            app(NotificationService::class)->send(
                $orgUser,
                new DonorResponseNotification($response),
                NotificationType::DONOR_RESPONSE
            );
        }

        return $response;
    }

    /**
     * Donor ignores a blood request.
     */
    public function ignore(Donor $donor, BloodRequest $request): void
    {
        if (! $request->isActive()) {
            throw new RuntimeException('هذا الطلب غير متاح الآن');
        }

        DB::transaction(function () use ($donor, $request) {
            $response = RequestResponse::query()->updateOrCreate(
                [
                    'donor_id' => $donor->id,
                    'blood_request_id' => $request->id,
                ],
                [
                    'status' => RequestResponseStatus::IGNORED->value,
                    'responded_at' => now(),
                ]
            );

            $this->qrCodeService->revoke($response);
        });
    }

    /**
     * Donor cancels a previously accepted request.
     */
    public function cancel(Donor $donor, BloodRequest $request): void
    {
        DB::transaction(function () use ($donor, $request) {
            $response = RequestResponse::query()
                ->where('donor_id', $donor->id)
                ->where('blood_request_id', $request->id)
                ->first();

            if (! $response || $response->status !== RequestResponseStatus::PENDING) {
                throw new RuntimeException('لا يمكن التراجع عن هذا الطلب');
            }

            $this->qrCodeService->revoke($response);

            $response->delete();
        });
    }
}
