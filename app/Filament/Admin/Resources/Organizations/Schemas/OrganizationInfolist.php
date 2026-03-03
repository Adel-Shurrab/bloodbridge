<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('هوية المنظمة')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->components([
                        TextEntry::make('org_name')
                            ->label('اسم المنظمة')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpan(2),

                        TextEntry::make('slug')
                            ->label('المعرّف (Slug)')
                            ->copyable()
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('license_number')
                            ->label('رقم الترخيص')
                            ->copyable()
                            ->icon('heroicon-o-document-text'),

                        TextEntry::make('governorate.name')
                            ->label('المحافظة')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('description')
                            ->label('وصف المنظمة')
                            ->columnSpanFull()
                            ->placeholder('لا يوجد وصف'),
                    ]),

                Section::make('حالة الموافقة')
                    ->icon('heroicon-o-shield-check')
                    ->columns(3)
                    ->components([

                        TextEntry::make('approval_status')
                            ->label('الحالة')
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('approved_at')
                            ->label('تاريخ الاعتماد')
                            ->dateTime('Y/m/d')
                            ->placeholder('لم يتم الاعتماد بعد')
                            ->icon('heroicon-o-check-badge'),

                        TextEntry::make('approvedBy.name')
                            ->label('اعتمد بواسطة')
                            ->placeholder('—')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->placeholder('—')
                            ->color('danger')
                            ->visible(fn($record) => filled($record?->rejection_reason)),

                        TextEntry::make('created_at')
                            ->label('تاريخ التسجيل')
                            ->dateTime('Y/m/d')
                            ->since()
                            ->icon('heroicon-o-clock'),
                    ]),

                Section::make('المسؤول')
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->components([
                        TextEntry::make('responsible_person_name')
                            ->label('الاسم')
                            ->weight('semibold'),

                        TextEntry::make('responsible_person_position')
                            ->label('المنصب')
                            ->placeholder('—'),

                        TextEntry::make('responsible_person_email')
                            ->label('البريد الشخصي')
                            ->icon('heroicon-o-envelope')
                            ->url(fn($state) => $state ? 'mailto:' . $state : null)
                            ->placeholder('—'),
                    ]),

                Section::make('معلومات التواصل')
                    ->icon('heroicon-o-phone-arrow-up-right')
                    ->columns(3)
                    ->components([
                        TextEntry::make('user.email')
                            ->label('البريد الرسمي للحساب')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->url(fn($state) => $state ? 'mailto:' . $state : null),

                        TextEntry::make('contact_email')
                            ->label('البريد الرسمي للتواصل')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->url(fn($state) => $state ? 'mailto:' . $state : null),

                        TextEntry::make('contact_phone')
                            ->label('هاتف التواصل')
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->url(fn($state) => $state ? 'tel:' . $state : null),
                    ]),

                // ── Working Hours + Capacity ────────────────────────────────────
                Section::make('أوقات العمل والطاقة الاستيعابية')
                    ->icon('heroicon-o-clock')
                    ->columns(4)
                    ->components([
                        TextEntry::make('opening_time')
                            ->label('وقت الفتح')
                            ->placeholder('غير محدد'),

                        TextEntry::make('closing_time')
                            ->label('وقت الإغلاق')
                            ->placeholder('غير محدد'),

                        TextEntry::make('daily_capacity')
                            ->label('الطاقة اليومية')
                            ->formatStateUsing(fn($state) => $state ? $state . ' متبرع/يوم' : '—'),

                        TextEntry::make('working_days')
                            ->label('أيام العمل')
                            ->getStateUsing(function ($record) {
                                $names = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                                $raw  = $record->working_days;
                                $days = is_array($raw) ? $raw : json_decode($raw ?? '[]', true);
                                if (! is_array($days) || empty($days)) return '—';
                                return implode('، ', array_map(fn($d) => $names[(int) $d] ?? $d, $days));
                            }),
                    ]),

                // ── Statistics ──────────────────────────────────────────────────
                Section::make('الإحصائيات')
                    ->icon('heroicon-o-chart-bar-square')
                    ->columns(3)
                    ->components([
                        TextEntry::make('total_request_created')
                            ->label('إجمالي الطلبات المنشأة')
                            ->formatStateUsing(fn($state) => number_format($state) . ' طلب')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('total_donation_verified')
                            ->label('إجمالي التبرعات المُوثّقة')
                            ->formatStateUsing(fn($state) => number_format($state) . ' تبرع')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('bloodRequests_count')
                            ->label('الطلبات النشطة حالياً')
                            ->getStateUsing(fn($record) => $record->bloodRequests()->active()->count())
                            ->formatStateUsing(fn($state) => $state . ' طلب')
                            ->badge()
                            ->color(fn($state) => $state > 0 ? 'warning' : 'gray'),
                    ]),

                // ── Location ────────────────────────────────────────────────────
                Section::make('الموقع الجغرافي')
                    ->icon('heroicon-o-map-pin')
                    ->collapsed()
                    ->columns(3)
                    ->components([
                        TextEntry::make('auto_location_address')
                            ->label('العنوان')
                            ->columnSpanFull()
                            ->placeholder('لا يوجد عنوان'),

                        TextEntry::make('lat')
                            ->label('خط العرض')
                            ->placeholder('غير محدد'),

                        TextEntry::make('lng')
                            ->label('خط الطول')
                            ->placeholder('غير محدد'),

                        TextEntry::make('lat')
                            ->label('رابط خرائط Google')
                            ->formatStateUsing(fn($record) => $record->lat
                                ? "({$record->lat}, {$record->lng})"
                                : 'لا توجد إحداثيات')
                            ->url(fn($record) => $record->lat
                                ? "https://www.google.com/maps/search/?api=1&query={$record->lat},{$record->lng}"
                                : null, true)
                            ->placeholder('لا توجد إحداثيات'),
                    ]),
            ]);
    }
}
