<?php

namespace App\Filament\Resources\Donors\Schemas;

use Filament\Schemas\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات شخصية')
                    ->icon('heroicon-o-user')
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('user.name')
                                    ->label('الاسم الكامل'),
                                TextEntry::make('national_id')
                                    ->label('رقم الهوية')
                                    ->copyable(),
                                TextEntry::make('gender')
                                    ->label('الجنس')
                                    ->formatStateUsing(fn($state) => \App\Models\Donor::getGenderOptions()[$state] ?? $state),
                                TextEntry::make('user.email')
                                    ->label('البريد الإلكتروني')
                                    ->copyable(),
                                TextEntry::make('user.phone')
                                    ->label('رقم الهاتف')
                                    ->url(fn($state) => 'tel:' . $state),
                                TextEntry::make('birth_date')
                                    ->label('تاريخ الميلاد')
                                    ->date('Y/m/d')
                            ]),
                    ]),

                Section::make('الملف الصحي')
                    ->icon('heroicon-o-heart')
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('healthProfile.blood_type')
                                    ->label('فصيلة الدم')
                                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                                    ->badge()
                                    ->color(Color::Red),
                                TextEntry::make('healthProfile.weight')
                                    ->label('الوزن (كجم)')
                                    ->suffix(' كجم'),
                                TextEntry::make('healthProfile.height')
                                    ->label('الطول (سم)')
                                    ->suffix(' سم'),
                                TextEntry::make('healthProfile.is_eligible')
                                    ->label('الحالة الصحية')
                                    ->formatStateUsing(fn($state) => $state ? 'مؤهل للتبرع' : 'غير مؤهل')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'danger'),
                                TextEntry::make('healthProfile.total_donations')
                                    ->label('عدد التبرعات السابقة'),
                            ]),
                    ]),

                Section::make('بيانات النظام')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed() // Optionally collapsed by default
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('level')
                                    ->label('المستوى')
                                    ->formatStateUsing(fn($state) => number_format($state)),
                                TextEntry::make('points')
                                    ->label('النقاط')
                                    ->formatStateUsing(fn($state) => number_format($state)),
                                TextEntry::make('created_at')
                                    ->label('تاريخ التسجيل')
                                    ->dateTime('Y/m/d H:i')
                                    ->since(),
                            ]),
                    ]),

                Section::make('الموقع الجغرافي')
                    ->icon('heroicon-o-map-pin')
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('governorate.name')
                                    ->label('المحافظة'),
                                TextEntry::make('lat')
                                    ->label('الإحداثيات')
                                    ->formatStateUsing(fn($record) => $record->lat . ', ' . $record->lng)
                                    ->url(fn($record) => "https://www.google.com/maps/search/?api=1&query={$record->lat},{$record->lng}", true),
                            ])
                    ])
            ]);
    }
}
