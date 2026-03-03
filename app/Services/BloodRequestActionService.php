<?php

namespace App\Services;

use App\Enums\RequestResponseStatus;
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

        if (! $donor->healthProfile?->is_eligible) {
            throw new RuntimeException('غير مؤهل للتبرع حاليًا');
        }

        // Prevent accepting more than one request at a time
        $alreadyAccepted = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('blood_request_id', '!=', $request->id)
            ->where('status', RequestResponseStatus::PENDING->value)
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

            // Generate QR code
            $this->qrCodeService->generate($response, true);

            return $response;
        });

        // Notify the organization's user that a donor accepted their request.
        // Done OUTSIDE the transaction so DB is committed before the notification is queued.
        $orgUser = $request->organization?->user;
        if ($orgUser) {
            $response->load(['donor.user', 'donor.healthProfile', 'bloodRequest.organization']);
            $orgUser->notify(new DonorResponseNotification($response));
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

            // Revoke QR code if it exists
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

            // Revoke QR code before deleting
            $this->qrCodeService->revoke($response);

            // Delete the response entirely — donor is free to accept any request again
            $response->delete();
        });
    }
}
