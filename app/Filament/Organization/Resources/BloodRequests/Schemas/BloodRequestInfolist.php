<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BloodRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('تفاصيل الطلب')
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->components([

                        TextEntry::make('blood_type')
                            ->label('فصيلة الدم')
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('urgency_level')
                            ->label('مستوى الاستعجال')
                            ->badge()
                            ->formatStateUsing(fn($state) => method_exists($state, 'getLabel') ? $state->getLabel() : $state)
                            ->color(fn($state) => match ((int) (is_object($state) ? $state->value : $state)) {
                                4 => 'danger',
                                3 => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('units_needed')
                            ->label('الوحدات المطلوبة')
                            ->formatStateUsing(fn($state) => $state . ' وحدة'),

                        TextEntry::make('donors_accepted')
                            ->label('متبرعون قبلوا')
                            ->formatStateUsing(fn($state) => $state . ' متبرع')
                            ->badge()
                            ->color('info')
                            ->getStateUsing(fn($record) => $record->responses()->where('status', \App\Enums\RequestResponseStatus::ACCEPTED)->count()),

                        TextEntry::make('donors_completed')
                            ->label('تبرعات مكتملة')
                            ->formatStateUsing(fn($state) => $state . ' تبرع')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('donors_found')
                            ->label('إجمالي المستجيبين')
                            ->getStateUsing(fn($record) => $record->responses()->count())
                            ->formatStateUsing(fn($state) => $state . ' شخص')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('additional_notes')
                            ->label('ملاحظات إضافية')
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
                    ]),

                Section::make('مواعيد الطلب')
                    ->icon('heroicon-o-clock')
                    ->columns(4)
                    ->components([

                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('broadcasted_at')
                            ->label('تاريخ البث')
                            ->dateTime('Y/m/d H:i')
                            ->placeholder('لم يُبَث بعد')
                            ->icon('heroicon-o-megaphone'),

                        TextEntry::make('fulfilled_at')
                            ->label('تاريخ الإتمام')
                            ->dateTime('Y/m/d H:i')
                            ->placeholder('—')
                            ->icon('heroicon-o-check-circle'),

                        TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path'),
                    ]),

                Section::make('نطاق البحث')
                    ->icon('heroicon-o-signal')
                    ->columns(3)
                    ->components([

                        TextEntry::make('search_radius_km')
                            ->label('نطاق البحث الأصلي')
                            ->formatStateUsing(fn($state) => $state . ' كم'),

                        TextEntry::make('actual_search_radius_km')
                            ->label('نطاق البحث الفعلي')
                            ->formatStateUsing(fn($state) => $state ? $state . ' كم' : '—')
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : null)
                            ->visible(fn($record) => $record->status === \App\Enums\BloodRequestStatus::BROADCASTED || $record->status === \App\Enums\BloodRequestStatus::FULFILLED),

                        TextEntry::make('expansion_steps')
                            ->label('مرات التوسيع')
                            ->getStateUsing(fn($record) => $record->expansion_steps)
                            ->formatStateUsing(fn($state) => $state > 0 ? $state . ' مرة' : 'لم يتوسع')
                            ->badge()
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : 'gray')
                            ->visible(fn($record) => $record->status === \App\Enums\BloodRequestStatus::BROADCASTED || $record->status === \App\Enums\BloodRequestStatus::FULFILLED),
                    ]),

                Section::make('موقع الطلب')
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->components([

                        TextEntry::make('location_address')
                            ->label('العنوان التفصيلي')
                            ->columnSpanFull()
                            ->placeholder('لا يوجد عنوان'),

                        TextEntry::make('lat')
                            ->label('خط العرض')
                            ->placeholder('غير محدد'),

                        TextEntry::make('lng')
                            ->label('خط الطول')
                            ->placeholder('غير محدد'),

                        TextEntry::make('google_maps_link')
                            ->label('رابط خرائط Google')
                            ->getStateUsing(fn($record) => $record->lat
                                ? "({$record->lat}, {$record->lng})"
                                : null)
                            ->url(fn($record) => $record->lat
                                ? "https://www.google.com/maps?q={$record->lat},{$record->lng}"
                                : null, true)
                            ->placeholder('لا توجد إحداثيات'),
                    ]),
            ]);
    }
}
