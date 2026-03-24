<?php

namespace App\Filament\Organization\Pages;

use App\Enums\RequestResponseStatus;
use App\Models\Organization;
use App\Models\RequestResponse;
use App\Services\QRCodeService;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use BackedEnum;



class ScanDonorQR extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'filament.organization.pages.scan-donor-qr';

    public function getTitle(): string
    {
        return __('organization.scan_qr_code');
    }

    public static function getNavigationLabel(): string
    {
        return __('organization.scan_qr_code');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected static ?int $navigationSort = 2;

    public ?string $scannedCode = null;
    public ?RequestResponse $foundResponse = null;

    public function verifyQRCode($code = null): bool
    {
        $code = $code ?? $this->scannedCode;

        if (empty($code)) {
            return false;
        }

        if (!$this->checkRateLimit()) {
            return false;
        }

        $qrService = app(QRCodeService::class);
        $organization = $this->getOrganization();

        if (! $organization) {
            $this->notify(__('organization.access_error'), __('organization.no_organization_tenant_active'), 'danger');
            return false;
        }

        $response = $qrService->validate($code, $organization);

        if (!$response) {
            $this->notify(__('organization.invalid_qr_code'), __('organization.code_invalid_or_expired_or_wrong_org'), 'danger');
            $this->foundResponse = null;
            return false;
        }

        if (!$this->validateDonorStatus($response)) {
            $this->foundResponse = null;
            return false;
        }

        $this->foundResponse = $response;
        $this->scannedCode = null; 

        return true;
    }

    public function confirmAdmission(): void
    {
        if (! $this->foundResponse) {
            return;
        }

        $organization = $this->getOrganization();

        if (! $organization) {
            abort(403);
        }

        $token = $this->foundResponse->verification_qr_code;
        $qrService = app(QRCodeService::class);

        $response = filled($token) ? $qrService->validate($token, $organization) : null;

        if (! $response || $response->id !== $this->foundResponse->id) {
            $this->notify(__('organization.invalid_qr_code'), __('organization.code_invalid_or_expired_or_wrong_org'), 'danger');
            $this->resetState();
            return;
        }

        if ($response->status !== RequestResponseStatus::PENDING) {
            $this->notify(__('organization.invalid_status'), __('organization.donor_status_does_not_allow_confirmation'), 'warning');
            $this->resetState();
            return;
        }

        $response->update([
            'status' => RequestResponseStatus::ACCEPTED,
            'verified_at' => now(),
        ]);

        $this->notify(
            __('organization.attendance_registered'),
            __('organization.donor_name_label', ['name' => $response->donor->user->name]),
            'success',
            true
        );

        $this->resetState();
    }

    public function cancelAdmission(): void
    {
        $this->resetState();
    }

    private function resetState(): void
    {
        $this->foundResponse = null;
        $this->scannedCode = null;
    }

    private function checkRateLimit(): bool
    {
        $orgId = $this->getOrganizationId() ?? 'unknown';
        $rateLimitKey = 'qr-scan:org:' . $orgId;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            $this->notify(
                __('organization.rate_limit_exceeded'),
                __('organization.please_wait_seconds_before_trying_again', ['seconds' => $seconds]),
                'warning'
            );

            Log::warning('QR scan rate limit exceeded', [
                'organization_id' => $orgId,
            ]);

            return false;
        }

        RateLimiter::hit($rateLimitKey);
        return true;
    }

    private function validateDonorStatus(RequestResponse $response): bool
    {
        return match ($response->status) {
            RequestResponseStatus::ACCEPTED => $this->notifyAndFail(__('organization.already_scanned'), __('organization.donor_already_registered_attendance', ['name' => $response->donor->user->name]), 'warning'),
            RequestResponseStatus::COMPLETED => $this->notifyAndFail(__('organization.donation_completed'), __('organization.donor_already_completed_donation'), 'info'),
            RequestResponseStatus::DECLINED => $this->notifyAndFail(__('organization.previously_excluded'), __('organization.donor_medically_excluded'), 'danger'),
            RequestResponseStatus::PENDING => true, 
            default => $this->notifyAndFail(__('organization.invalid_status'), __('organization.donor_status_does_not_allow_confirmation'), 'warning'),
        };
    }

    private function notifyAndFail(string $title, string $body, string $type): bool
    {
        $this->notify($title, $body, $type);
        return false;
    }

    private function notify(string $title, string $body, string $type, bool $persistent = false): void
    {
        $notification = Notification::make()
            ->title($title)
            ->body($body)
            ->$type();

        if ($persistent) {
            $notification->persistent();
        }

        $notification->send();
    }

    protected function getOrganization(): ?Organization
    {
        $tenant = filament()->getTenant();

        if ($tenant instanceof Organization) {
            return $tenant;
        }

        return Auth::user()?->organization;
    }

    protected function getOrganizationId(): ?int
    {
        return $this->getOrganization()?->getKey();
    }
}
