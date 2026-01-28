<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class ViewBloodRequest extends ViewRecord
{
    protected static string $resource = BloodRequestResource::class;

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Request Details')
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('blood_type')
                                    ->label('فصيلة الدم')
                                    ->badge(),
                                TextEntry::make('units_needed')
                                    ->label('الوحدات المطلوبة'),
                                TextEntry::make('urgency_level')
                                    ->label('الاستعجال')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => $this->record->getUrgencyOptions()[$state] ?? $state)
                                    ->color(fn($state) => match ((int)$state) {
                                        4 => 'danger',
                                        3 => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('status')
                                    ->label('الحالة')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => $this->record->getStatusOptions()[$state] ?? $state),
                                TextEntry::make('additional_notes')
                                    ->label('ملاحظات إضافية')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('نطاق البحث عن المتبرعين')
                            ->columnSpan(3)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('search_radius_km')
                                            ->label('النطاق الأولي')
                                            ->suffix(' كم')
                                            ->badge()
                                            ->color('gray'),
                                        TextEntry::make('actual_search_radius_km')
                                            ->label('النطاق النهائي')
                                            ->suffix(' كم')
                                            ->badge()
                                            ->color(fn() => $this->record->wasExpanded() ? 'warning' : 'success')
                                            ->visible(fn() => $this->record->actual_search_radius_km !== null),
                                        TextEntry::make('expansion_steps')
                                            ->label('مرات التوسع')
                                            ->suffix(fn($state) => $state > 0 ? ' ' . ($state > 1 ? 'مرات' : 'مرة') : '')
                                            ->badge()
                                            ->color(fn($state) => $state > 0 ? 'warning' : 'success')
                                            ->visible(fn() => $this->record->expansion_steps !== null),
                                        TextEntry::make('donors_found')
                                            ->label('المتبرعين المحتملين')
                                            ->badge()
                                            ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                                            ->visible(fn() => $this->record->donors_found !== null),
                                    ]),
                            ])
                            ->visible(fn() => $this->record->status === \App\Enums\BloodRequestStatus::BROADCASTED),

                        Section::make('Statistics')
                            ->columnSpan(3)
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('donors_completed')
                                            ->label('تحقق التبرع')
                                            ->suffix(' / ' . $this->record->units_needed),
                                        TextEntry::make('created_at')
                                            ->label('تاريخ الطلب')
                                            ->dateTime(),
                                        TextEntry::make('broadcasted_at')
                                            ->label('تاريخ البث')
                                            ->dateTime()
                                            ->visible(fn() => $this->record->broadcasted_at !== null),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
