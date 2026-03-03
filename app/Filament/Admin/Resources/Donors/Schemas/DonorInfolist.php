<?php

namespace App\Filament\Admin\Resources\Donors\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Icons\Heroicon;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('معلومات شخصية')
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->components([
                        TextEntry::make('user.name')
                            ->label('الاسم الكامل')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('national_id')
                            ->label('رقم الهوية الوطنية')
                            ->copyable()
                            ->copyMessage('تم النسخ!')
                            ->icon('heroicon-o-identification'),

                        TextEntry::make('gender')
                            ->label('الجنس')
                            ->badge(),

                        TextEntry::make('user.email')
                            ->label('البريد الإلكتروني')
                            ->copyable()
                            ->icon('heroicon-o-envelope')
                            ->url(fn($state) => 'mailto:' . $state),

                        TextEntry::make('user.phone')
                            ->label('رقم الهاتف')
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->url(fn($state) => 'tel:' . $state),

                        TextEntry::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->date('Y/m/d')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('governorate.name')
                            ->label('المحافظة')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-map-pin'),

                        TextEntry::make('auto_location_address')
                            ->label('العنوان')
                            ->columnSpan(2)
                            ->placeholder('لا يوجد عنوان مسجّل'),
                    ]),

                Section::make('بيانات النظام')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(4)
                    ->components([
                        TextEntry::make('points')
                            ->label('النقاط المتراكمة')
                            ->formatStateUsing(fn($state) => number_format($state) . ' نقطة')
                            ->badge()
                            ->color('warning')
                            ->size('lg'),

                        TextEntry::make('level')
                            ->label('مستوى المتبرع')
                            ->formatStateUsing(fn($state) => 'المستوى ' . $state)
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('created_at')
                            ->label('تاريخ التسجيل')
                            ->dateTime('Y/m/d')
                            ->since()
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('Y/m/d H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ]),


                // ── Health Profile ──────────────────────────────────────────────
                Section::make('الملف الصحي')
                    ->icon('heroicon-o-heart')
                    ->columns(4)
                    ->components([

                        // Blood type — self-declared
                        TextEntry::make('healthProfile.blood_type')
                            ->label('فصيلة الدم (المعلنة)')
                            ->badge()
                            ->size('xl'),

                        // Blood type — hospital-verified
                        TextEntry::make('healthProfile.verified_blood_type')
                            ->label('فصيلة الدم (موثّقة)')
                            ->badge()
                            ->color('success')
                            ->placeholder('غير موثّقة بعد'),

                        TextEntry::make('healthProfile.weight')
                            ->label('الوزن')
                            ->suffix(' كجم')
                            ->formatStateUsing(fn($state) => $state ? $state . ' كجم' : '—')
                            ->color(fn($state) => $state && $state < 50 ? 'danger' : null),

                        TextEntry::make('healthProfile.height')
                            ->label('الطول')
                            ->formatStateUsing(fn($state) => $state ? $state . ' سم' : '—')
                            ->color(fn($state) => $state && $state < 140 ? 'danger' : null),

                        // Eligibility status — the most important field
                        TextEntry::make('healthProfile.is_eligible')
                            ->label('أهلية التبرع')
                            ->formatStateUsing(fn($state) => $state ? '✓ مؤهل للتبرع' : '✗ غير مؤهل حالياً')
                            ->badge()
                            ->size('lg')
                            ->color(fn($state) => $state ? 'success' : 'danger'),

                        TextEntry::make('healthProfile.next_eligible_date')
                            ->label('تاريخ الأهلية التالية')
                            ->date('Y/m/d')
                            ->placeholder('—')
                            ->icon('heroicon-o-calendar-days')
                            ->color('warning'),

                        TextEntry::make('healthProfile.total_donations')
                            ->label('إجمالي التبرعات')
                            ->formatStateUsing(fn($state) => $state . ' تبرع')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('healthProfile.last_donation_date')
                            ->label('آخر تبرع')
                            ->date('Y/m/d')
                            ->since()
                            ->placeholder('لا يوجد')
                            ->icon('heroicon-o-clock'),

                        // Health flags grid
                        TextEntry::make('healthProfile.chronic_disease')
                            ->label('مرض مزمن')
                            ->formatStateUsing(fn($state) => $state ? 'نعم' : 'لا')
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),

                        TextEntry::make('healthProfile.infection')
                            ->label('إصابة حالية')
                            ->formatStateUsing(fn($state) => $state ? 'نعم' : 'لا')
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),

                        TextEntry::make('healthProfile.has_recent_surgery')
                            ->label('جراحة حديثة')
                            ->formatStateUsing(fn($state) => $state ? 'نعم' : 'لا')
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'success'),

                        TextEntry::make('healthProfile.recent_donation')
                            ->label('تبرع حديث (< 90 يوم)')
                            ->formatStateUsing(fn($state) => $state ? 'نعم' : 'لا')
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'success'),
                    ]),

                // ── Location ────────────────────────────────────────────────────
                Section::make('الموقع الجغرافي')
                    ->icon('heroicon-o-map-pin')
                    ->collapsed()
                    ->columns(3)
                    ->components([
                        TextEntry::make('governorate.name')
                            ->label('المحافظة'),

                        TextEntry::make('lat')
                            ->label('خط العرض')
                            ->placeholder('غير محدد'),

                        TextEntry::make('lng')
                            ->label('خط الطول')
                            ->placeholder('غير محدد'),

                        TextEntry::make('lat')
                            ->label('رابط خرائط Google')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($record) => $record->lat
                                ? "({$record->lat}, {$record->lng})"
                                : 'لا توجد إحداثيات مسجّلة')
                            ->url(fn($record) => $record->lat
                                ? "https://www.google.com/maps/search/?api=1&query={$record->lat},{$record->lng}"
                                : null, true)
                            ->placeholder('لا توجد إحداثيات'),
                    ]),
            ]);
    }
}
