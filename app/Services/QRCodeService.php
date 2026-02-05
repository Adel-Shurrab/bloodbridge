<?php

namespace App\Services;

use App\Models\RequestResponse;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QRCodeService
{
    private const DEFAULT_EXPIRATION_DAYS = 7;

    /**
     * Generate a unique QR code for a response
     *
     * @param RequestResponse $response The response to generate QR for
     * @param bool $setExpiration Whether to set expiration based on blood request deadline
     * @return string The generated QR code
     * @throws \RuntimeException If blood request is missing when expiration is required
     */
    public function generate(RequestResponse $response, bool $setExpiration = true): string
    {
        // Validate prerequisites
        if ($setExpiration && !$response->bloodRequest) {
            throw new \RuntimeException('Cannot set expiration without associated blood request');
        }

        // Generate cryptographically secure QR code
        $qrCode = Str::uuid()->toString();
        $expiresAt = $setExpiration ? $this->calculateExpiration($response) : null;

        // Update response with QR code
        $response->update([
            'verification_qr_code' => $qrCode,
            'qr_code_expires_at' => $expiresAt,
        ]);

        Log::info('QR code generated', [
            'response_id' => $response->id,
            'qr_code' => $qrCode,
            'expires_at' => $expiresAt?->toDateTimeString(),
        ]);

        return $qrCode;
    }

    /**
     * Validate a QR code and return the associated response if valid
     *
     * @param string $code The QR code to validate
     * @param Organization $organization The organization attempting to scan
     * @return RequestResponse|null The response if valid, null otherwise
     */
    public function validate(string $code, Organization $organization): ?RequestResponse
    {
        return RequestResponse::where('verification_qr_code', $code)
            ->where(function ($q) {
                // Accept QR codes with no expiration OR not yet expired
                $q->whereNull('qr_code_expires_at')
                    ->orWhere('qr_code_expires_at', '>', now());
            })
            ->whereHas('bloodRequest', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->with(['donor.user', 'donor.healthProfile'])
            ->first();
    }

    /**
     * Revoke a QR code (mark as expired immediately)
     *
     * @param RequestResponse $response The response whose QR should be revoked
     * @return void
     */
    public function revoke(RequestResponse $response): void
    {
        if (!$response->verification_qr_code) {
            return;
        }

        // Set expiration to now (immediately invalid)
        $response->update([
            'qr_code_expires_at' => now(),
        ]);

        Log::info('QR code revoked', [
            'response_id' => $response->id,
            'qr_code' => $response->verification_qr_code,
        ]);
    }

    /**
     * Calculate expiration date based on blood request creation date
     *
     * @param RequestResponse $response
     * @return Carbon
     */
    private function calculateExpiration(RequestResponse $response): Carbon
    {
        // Use blood request creation date + default expiration days
        return $response->bloodRequest->created_at->copy()->addDays(self::DEFAULT_EXPIRATION_DAYS);
    }
}
