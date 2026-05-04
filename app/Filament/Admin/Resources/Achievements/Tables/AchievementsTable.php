<?php

namespace App\Filament\Admin\Resources\Achievements\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AchievementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('badge_icon')
                    ->label(__('admin.badge_icon_image'))
                    ->disk('public')
                    ->height(48)
                    ->width(48),

                TextColumn::make('name')
                    ->label(__('admin.achievement_name'))
                    ->getStateUsing(fn($record, $livewire) =>
                        $record->getTranslation('name', $livewire?->activeLocale ?? app()->getLocale(), false)
                        ?: $record->getTranslation('name', 'ar', false)
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('criteria_type')
                    ->label(__('admin.criteria_type'))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'donations'         => 'danger',
                        'points'            => 'success',
                        'critical_donations'=> 'warning',
                        'rare_blood_type'   => 'primary',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn($state) => __("admin.criteria_{$state}")),

                TextColumn::make('criteria_value')
                    ->label(__('admin.criteria_value'))
                    ->sortable(),

                TextColumn::make('points_rewards')
                    ->label(__('admin.points_rewards'))
                    ->sortable(),

                TextColumn::make('badge_type')
                    ->label(__('admin.badge_type'))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'bronze'   => 'warning',
                        'silver'   => 'gray',
                        'gold'     => 'success',
                        'platinum' => 'info',
                        'diamond'  => 'primary',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn($state) => __("admin.badge_{$state}")),

                TextColumn::make('donorAchievements_count')
                    ->counts('donorAchievements')
                    ->label(__('admin.earned_by_donors'))
                    ->sortable(),

                TextColumn::make('display_order')
                    ->label(__('admin.display_order'))
                    ->sortable(),
            ])
            ->defaultSort('display_order', 'asc')
            ->recordActions([
                EditAction::make()->label(__('admin.edit')),
                // No DeleteAction — achievements table has no deleted_at column
            ]);
    }
}
