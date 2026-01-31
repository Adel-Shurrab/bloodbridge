<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\RequestResponse;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class RecentResponsesWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $organization = Auth::user()->organization;

        return $table
            ->heading('آخر الاستجابات')
            ->description('أحدث 5 استجابات من المتبرعين')
            ->query(
                RequestResponse::query()
                    ->whereHas('bloodRequest', function ($query) use ($organization) {
                        $query->where('organization_id', $organization->id);
                    })
                    ->with(['donor.user', 'bloodRequest'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('donor.user.name')
                    ->label('اسم المتبرع')
                    ->getStateUsing(fn($record) => $record->donor?->user?->name)
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('bloodRequest.blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel()),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->colors([
                        'warning' => \App\Enums\RequestResponseStatus::PENDING->value,
                        'success' => \App\Enums\RequestResponseStatus::ACCEPTED->value,
                        'primary' => \App\Enums\RequestResponseStatus::COMPLETED->value,
                        'danger' => \App\Enums\RequestResponseStatus::DECLINED->value,
                        'gray' => \App\Enums\RequestResponseStatus::UNREACHABLE->value,
                    ]),

                TextColumn::make('created_at')
                    ->label('تاريخ الاستجابة')
                    ->dateTime('Y-m-d H:i')
                    ->since()
                    ->icon('heroicon-o-clock'),
            ])
            ->actions([
                ViewAction::make('view')
                    ->label('عرض التفاصيل')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('تفاصيل استجابة المتبرع')
                    ->form([
                        Section::make('معلومات المتبرع')
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('donor_name')
                                            ->label('اسم المتبرع')
                                            ->content(fn($record) => $record->donor?->user?->name),
                                        Placeholder::make('donor_phone')
                                            ->label('رقم الهاتف')
                                            ->content(fn($record) => $record->donor?->user?->phone ?? 'غير متوفر'),
                                    ]),
                            ]),
                        Section::make('تفاصيل الفصيلة والتحقق')
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('blood_type')
                                            ->label('فصيلة الدم (المصرح بها)')
                                            ->content(fn($record) => $record->donor?->healthProfile?->blood_type?->getLabel() ?? 'غير معروف'),
                                        Placeholder::make('verified_blood_type')
                                            ->label('الفصيلة المؤكدة')
                                            ->content(fn($record) => $record->donor?->healthProfile?->verified_blood_type?->getLabel() ?? 'لم يتم التحقق مخبرياً'),
                                    ]),
                            ]),
                        Section::make('الحالة والتوقيت')
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('status')
                                            ->label('حالة الاستجابة')
                                            ->content(fn($record) => $record->status->getLabel()),
                                        Placeholder::make('created_at')
                                            ->label('وقت الاستجابة')
                                            ->content(fn($record) => $record->created_at->format('Y-m-d H:i')),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
