<?php

namespace App\Services;

use App\Enums\BloodRequestStatus;
use App\Enums\RequestResponseStatus;
use App\Models\RequestResponse;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    private const DEFAULT_EXPIRATION_DAYS = 7;

    public function generate(RequestResponse $response, bool $setExpiration = true): string
    {
        $response->loadMissing('bloodRequest');

        if (! $response->bloodRequest) {
            throw new \RuntimeException('Response has no associated blood request.');
        }

        $responseStatus = $this->asRequestResponseStatus($response->status);

        if ($responseStatus !== RequestResponseStatus::PENDING) {
            throw new \RuntimeException('QR can only be generated for PENDING responses.');
        }

        $requestStatus = $this->asBloodRequestStatus($response->bloodRequest->status);

        if (! in_array($requestStatus, [
            BloodRequestStatus::BROADCASTED,
        ], true)) {
            throw new \RuntimeException('Cannot generate QR for inactive blood request.');
        }

        $qrCode = bin2hex(random_bytes(16));
        $expiresAt = $setExpiration ? $this->calculateExpiration($response) : null;

        $response->update([
            'verification_qr_code' => $qrCode,
            'qr_code_expires_at' => $expiresAt,
        ]);

        Log::info('QR code generated', [
            'response_id' => $response->id,
            'expires_at' => $expiresAt?->toDateTimeString(),
        ]);

        return $qrCode;
    }

    /**
     * Render the QR code as an SVG string.
     */
    public function render(string $token): string
    {
        return QrCode::format('svg')
            ->size(420)
            ->margin(2)
            ->generate($token);
    }

    public function validate(string $code, Organization $organization): ?RequestResponse
    {
        return RequestResponse::query()
            ->where('verification_qr_code', $code)
            
            ->where(function ($q) {
                $q->whereNull('qr_code_expires_at')
                    ->orWhere('qr_code_expires_at', '>', now());
            })
            ->whereHas('bloodRequest', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id)
                    ->whereIn('blood_requests.status', [
                        BloodRequestStatus::BROADCASTED->value,
                    ])
                    ->whereNull('fulfilled_at');
            })
            ->with(['donor.user', 'donor.healthProfile', 'bloodRequest'])
            ->first();
    }

    public function revoke(RequestResponse $response): void
    {
        if (! $response->verification_qr_code) {
            return;
        }

        $response->update([
            'verification_qr_code' => null,
            'qr_code_expires_at' => null,
        ]);

        Log::info('QR code revoked', [
            'response_id' => $response->id,
        ]);
    }

    private function calculateExpiration(RequestResponse $response): Carbon
    {
        $base = $response->responded_at ?? now();
        return Carbon::parse($base)->copy()->addDays(self::DEFAULT_EXPIRATION_DAYS);
    }

    /**
     * Normalize response status (Enum OR int) into Enum
     */
    private function asRequestResponseStatus(RequestResponseStatus|int|null $status): RequestResponseStatus
    {
        if ($status instanceof RequestResponseStatus) {
            return $status;
        }

        if ($status === null) {
            
            return RequestResponseStatus::PENDING;
        }

        return RequestResponseStatus::from((int) $status);
    }

    /**
     * Normalize blood request status (Enum OR int) into Enum
     */
    private function asBloodRequestStatus(BloodRequestStatus|int|null $status): BloodRequestStatus
    {
        if ($status instanceof BloodRequestStatus) {
            return $status;
        }

        if ($status === null) {
            
            return BloodRequestStatus::PENDING;
        }

        return BloodRequestStatus::from((int) $status);
    }
}
