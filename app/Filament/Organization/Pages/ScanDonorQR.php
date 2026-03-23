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
        return __('Scan QR Code');
    }

    public static function getNavigationLabel(): string
    {
        return __('Scan QR Code');
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
            $this->notify(__('Access error'), __('No organization tenant is active for this session.'), 'danger');
            return false;
        }

        $response = $qrService->validate($code, $organization);

        if (!$response) {
            $this->notify(__('Invalid QR Code'), __('This code does not exist, is expired, or does not belong to this organization.'), 'danger');
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
            $this->notify(__('Invalid QR Code'), __('This code does not exist, is expired, or does not belong to this organization.'), 'danger');
            $this->resetState();
            return;
        }

        if ($response->status !== RequestResponseStatus::PENDING) {
            $this->notify(__('Invalid status'), __('The donor\'s current status does not allow confirmation.'), 'warning');
            $this->resetState();
            return;
        }

        $response->update([
            'status' => RequestResponseStatus::ACCEPTED,
            'verified_at' => now(),
        ]);

        $this->notify(
            __('Attendance Registered'),
            __('Donor: :name', ['name' => $response->donor->user->name]),
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
                __('Rate limit exceeded'),
                __('Please wait :seconds seconds before trying again.', ['seconds' => $seconds]),
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
            RequestResponseStatus::ACCEPTED => $this->notifyAndFail(__('Already scanned'), __('The donor :name has already registered their attendance.', ['name' => $response->donor->user->name]), 'warning'),
            RequestResponseStatus::COMPLETED => $this->notifyAndFail(__('Donation completed'), __('This donor has already completed the donation process.'), 'info'),
            RequestResponseStatus::DECLINED => $this->notifyAndFail(__('Previously excluded'), __('Sorry, this donor has been medically excluded.'), 'danger'),
            RequestResponseStatus::PENDING => true, 
            default => $this->notifyAndFail(__('Invalid status'), __('The donor\'s current status does not allow confirmation.'), 'warning'),
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
