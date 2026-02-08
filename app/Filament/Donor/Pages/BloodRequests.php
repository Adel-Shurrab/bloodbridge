<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodRequestStatus;
use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use App\Filament\Donor\Widgets\EligibilityCountdownWidget;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use App\Services\QRCodeService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Builder\Builder as QrBuilder;
use Endroid\QrCode\Writer\PngWriter;
class BloodRequests extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-megaphone';
    protected static ?string $navigationLabel = 'طلبات الدم';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.donor.pages.blood-requests';

    public function getHeading(): string
    {
        return '';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EligibilityCountdownWidget::class,
        ];
    }

    /**
     * Requests shown to donor:
     * - blood type match (verified -> fallback)
     * - status in [BROADCASTED, MATCHED]
     * - not fulfilled
     * - optionally sorted by distance (if donor has lat/lng)
     */
    protected function getRequestsQuery(): Builder
    {
        $donor = Auth::user()?->donor;
        $profile = $donor?->healthProfile;

        $donorBloodType = $profile?->verified_blood_type ?? $profile?->blood_type;

        $query = BloodRequest::query()
            ->with(['organization'])
            ->whereIn('status', [
                BloodRequestStatus::BROADCASTED,
                BloodRequestStatus::MATCHED,
            ])
            ->whereNull('fulfilled_at')
            ->when($donorBloodType !== null, fn(Builder $q) => $q->where('blood_type',  $donorBloodType));

        // ✅ Optional distance ordering if donor has location
        if (! is_null($donor?->lat) && ! is_null($donor?->lng)) {
            $lat = (float) $donor->lat;
            $lng = (float) $donor->lng;

            // Haversine (MySQL)
            $query->selectRaw(
                "blood_requests.*,
                 (6371 * acos(
                    cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(lat))
                 )) AS distance_km",
                [$lat, $lng, $lat]
            )
                ->orderByRaw('distance_km ASC');
        }

        return $query->orderByDesc('broadcasted_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getRequestsQuery())
            ->columns([
                Tables\Columns\TextColumn::make('organization.org_name')
                    ->label('المؤسسة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('distance_km')
                    ->label('المسافة')
                    ->state(
                        fn($record) => isset($record->distance_km)
                            ? number_format((float) $record->distance_km, 1) . ' كم'
                            : '—'
                    ),

                Tables\Columns\TextColumn::make('blood_type')
                    ->label('فصيلة الدم المطلوبة')
                    ->state(fn(BloodRequest $record) => $record->blood_type?->getLabel()),


                Tables\Columns\TextColumn::make('units_needed')
                    ->label('الكمية المطلوبة (وحدات)')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('حالة الطلب')
                    ->formatStateUsing(fn(BloodRequest $record) => $record->status->getLabel())
                    ->color(fn(BloodRequest $record) => $record->status->getColor()),


                Tables\Columns\BadgeColumn::make('my_status')
                    ->label('حالتك')
                    ->getStateUsing(fn(BloodRequest $record) => $this->getMyResponse($record)?->status)
                    ->formatStateUsing(
                        fn($state) => $state
                            ? $state->getLabel()
                            : 'لم ترد'
                    )
                    ->color(
                        fn($state) => $state
                            ? $state->getColor()
                            : 'gray'
                    ),

            ])
            ->actions([
                Action::make('accept')
                    ->label('قبول')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn(BloodRequest $record) => $this->canAccept($record))
                    ->disabled(fn() => ! $this->isEligibleNow())
                    ->action(fn(BloodRequest $record) => $this->accept($record)),

                Action::make('ignore')
                    ->label('تجاهل')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn(BloodRequest $record) => $this->canIgnore($record))
                    ->action(fn(BloodRequest $record) => $this->ignore($record)),

                Action::make('download_qr')
                    ->label('تنزيل QR')
                    ->icon('heroicon-m-qr-code')
                    ->visible(fn(BloodRequest $record) => $this->canDownloadQr($record))
                    ->action(fn(BloodRequest $record) => $this->downloadQr($record)),
            ]);
    }

    // -------------------------
    // Helpers
    // -------------------------

    protected function isEligibleNow(): bool
    {
        $profile = Auth::user()?->donor?->healthProfile;
        return (bool) ($profile?->is_eligible ?? false);
    }

    protected function getMyResponse(BloodRequest $request): ?RequestResponse
    {
        $donorId = Auth::user()?->donor?->id;

        if (! $donorId) {
            return null;
        }

        return RequestResponse::query()
            ->where('donor_id', $donorId)
            ->where('blood_request_id', $request->id)
            ->first();
    }

    protected function requestIsActive(BloodRequest $request): bool
    {
        return in_array($request->status, [
            BloodRequestStatus::BROADCASTED,
            BloodRequestStatus::MATCHED,
        ], true)
            && is_null($request->fulfilled_at)
            && is_null($request->deleted_at);
    }

    protected function canAccept(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getMyResponse($request);

        // ✅ allow accept if no response OR previously ignored
        if (! $response) {
            return true;
        }

        return $response->status === RequestResponseStatus::IGNORED;
    }

    protected function canIgnore(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getMyResponse($request);

        if (! $response) {
            return true;
        }

        if ($response->status === RequestResponseStatus::IGNORED) {
            return false;
        }

        // do not allow ignore after accept / verified / completed
        return ! in_array($response->status, [
            RequestResponseStatus::PENDING,
            RequestResponseStatus::ACCEPTED,
            RequestResponseStatus::COMPLETED,
        ], true);
    }

    protected function canDownloadQr(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getMyResponse($request);

        if (! $response) {
            return false;
        }

        if ($response->status !== RequestResponseStatus::PENDING) {
            return false;
        }

        if (! $response->verification_qr_code) {
            return false;
        }

        if ($response->verified_at) {
            return false;
        }

        if ($response->qr_code_expires_at && $response->qr_code_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    // -------------------------
    // Actions
    // -------------------------

    protected function accept(BloodRequest $request): void
    {
        if (! $this->requestIsActive($request)) {
            Notification::make()->danger()->title('هذا الطلب غير متاح الآن')->send();
            return;
        }

        if (! $this->isEligibleNow()) {
            Notification::make()
                ->danger()
                ->title('غير مؤهل للتبرع حاليًا')
                ->body('سيصبح زر القبول متاحًا عند عودتك للأهلية للتبرع.')
                ->send();
            return;
        }

        $donorId = Auth::user()?->donor?->id;

        if (! $donorId) {
            Notification::make()->danger()->title('تعذر تحديد بيانات المتبرع')->send();
            return;
        }

        // ✅ upsert (unique donor_id + blood_request_id)
        $response = RequestResponse::query()->updateOrCreate(
            [
                'donor_id' => $donorId,
                'blood_request_id' => $request->id,
            ],
            [
                'status' => RequestResponseStatus::PENDING->value, // وافق
                'responded_at' => now(),
            ]
        );

        // ✅ Generate token + expires via service (7 days by your service)
        try {
            /** @var QRCodeService $qr */
            $qr = app(QRCodeService::class);
            $qr->generate($response, true);
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('حدث خطأ أثناء إنشاء QR')
                ->body($e->getMessage())
                ->send();
            return;
        }

        Notification::make()
            ->success()
            ->title('تم قبول الطلب ✅')
            ->body('يمكنك الآن تنزيل رمز QR وإبرازه للمؤسسة عند الحضور.')
            ->send();
    }

    protected function ignore(BloodRequest $request): void
    {
        if (! $this->requestIsActive($request)) {
            Notification::make()->danger()->title('هذا الطلب غير متاح الآن')->send();
            return;
        }

        $donorId = Auth::user()?->donor?->id;

        if (! $donorId) {
            Notification::make()->danger()->title('تعذر تحديد بيانات المتبرع')->send();
            return;
        }

        $response = RequestResponse::query()->updateOrCreate(
            [
                'donor_id' => $donorId,
                'blood_request_id' => $request->id,
            ],
            [
                'status' => RequestResponseStatus::IGNORED->value, // متجاهل
                'responded_at' => now(),
            ]
        );

        // ✅ revoke any existing QR
        try {
            /** @var QRCodeService $qr */
            $qr = app(QRCodeService::class);
            $qr->revoke($response);
        } catch (\Throwable $e) {
            // ignore revoke errors
        }

        Notification::make()
            ->success()
            ->title('تم تجاهل الطلب')
            ->send();
    }

protected function downloadQr(BloodRequest $request)
{
    $response = $this->getMyResponse($request);

    if (! $response || ! $this->canDownloadQr($request)) {
        Notification::make()->danger()->title('لا يمكن تنزيل QR الآن')->send();
        return null;
    }

    $token = $response->verification_qr_code;

    // ✅ PNG بدون Imagick (Endroid)
    $result = QrBuilder::create()
        ->writer(new PngWriter())
        ->data($token)
        ->size(420)      // حجم مناسب للجوال
        ->margin(12)
        ->build();

    $filename = 'bloodbridge-qr-' . $request->id . '.png';

    return response()->streamDownload(function () use ($result) {
        echo $result->getString(); // raw png bytes
    }, $filename, [
        'Content-Type' => 'image/png',
    ]);
}

}
