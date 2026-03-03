<?php

namespace App\Filament\Organization\Pages;

use App\Enums\RequestResponseStatus;
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

    protected static ?string $title = 'مسح QR Code';

    protected static ?string $navigationLabel = 'مسح QR Code';

    protected static ?int $navigationSort = 2;

    public ?string $scannedCode = null;
    public ?RequestResponse $foundResponse = null;

    public function verifyQRCode($code = null): bool
    {
        $code = $code ?? $this->scannedCode;

        if (empty($code)) {
            return false;
        }

        // Rate limiting: 30 attempts per minute per organization
        if (!$this->checkRateLimit()) {
            return false;
        }

        // Use QRCodeService for validation (includes expiration check and org filter)
        $qrService = app(QRCodeService::class);
        $organization = filament()->getTenant() ?? Auth::user()->organization;

        $response = $qrService->validate($code, $organization);

        // Validation: QR code not found or invalid
        if (!$response) {
            $this->notify('QR Code غير صالح', 'هذا الكود غير موجود، منتهي الصلاحية، أو لا يتبع لهذه المنظمة.', 'danger');
            $this->foundResponse = null;
            return false;
        }

        // Status Validation: Check donor status
        if (!$this->validateDonorStatus($response)) {
            $this->foundResponse = null;
            return false;
        }

        // Found! Show details for confirmation
        $this->foundResponse = $response;
        $this->scannedCode = null; // Clear input

        return true;
    }

    public function confirmAdmission(): void
    {
        if (! $this->foundResponse) {
            return;
        }

        // Success: Update Status
        $this->foundResponse->update([
            'status' => RequestResponseStatus::ACCEPTED,
            'verified_at' => now(),
        ]);

        $this->notify(
            '✅ تم تسجيل الحضور',
            "المتبرع: {$this->foundResponse->donor->user->name}",
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
        $orgId = filament()->getTenant()?->id ?? Auth::user()->organization_id;
        $rateLimitKey = 'qr-scan:org:' . $orgId;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            $this->notify(
                'تم تجاوز الحد المسموح',
                "يرجى الانتظار {$seconds} ثانية قبل المحاولة مرة أخرى.",
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
            RequestResponseStatus::ACCEPTED => $this->notifyAndFail('تم المسح مسبقاً', "المتبرع {$response->donor->user->name} قام بتسجيل حضوره بالفعل.", 'warning'),
            RequestResponseStatus::COMPLETED => $this->notifyAndFail('تبرع مكتمل', "هذا المتبرع أتم عملية التبرع بالفعل.", 'info'),
            RequestResponseStatus::DECLINED => $this->notifyAndFail('مستبعد سابقاً', "عذراً، هذا المتبرع تم استبعاده طبياً.", 'danger'),
            RequestResponseStatus::PENDING => true, // Only valid status
            default => $this->notifyAndFail('حالة غير صالحة', "حالة المتبرع الحالية لا تسمح بالتأكيد.", 'warning'),
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
}
