<?php

namespace App\Filament\Admin\Resources\Achievements\Schemas;

use App\Models\Achievement;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make(__('admin.achievement_details'))
                ->description(__('admin.achievement_details_description'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('admin.achievement_name'))
                        ->required()
                        ->maxLength(100),

                    Textarea::make('description')
                        ->label(__('admin.achievement_description'))
                        ->rows(2)
                        ->maxLength(300),
                ]),

            Section::make(__('admin.achievement_badge'))
                ->schema([
                    // FileUpload replaces the old heroicon text input.
                    // Stores relative path like "achievements/first-drop.png"
                    FileUpload::make('badge_icon')
                        ->label(__('admin.badge_icon_image'))
                        ->helperText(__('admin.badge_icon_image_helper'))
                        ->image()
                        ->disk('public')
                        ->directory('achievements')
                        ->visibility('public')
                        ->imagePreviewHeight('80')
                        ->maxSize(512), // 512KB max

                    Select::make('badge_type')
                        ->label(__('admin.badge_type'))
                        ->options([
                            'bronze'   => __('admin.badge_bronze'),
                            'silver'   => __('admin.badge_silver'),
                            'gold'     => __('admin.badge_gold'),
                            'platinum' => __('admin.badge_platinum'),
                            'diamond'  => __('admin.badge_diamond'),
                        ])
                        ->native(false),

                    TextInput::make('display_order')
                        ->label(__('admin.display_order'))
                        ->numeric()
                        ->minValue(0)
                        ->default(Achievement::DEFAULT_DISPLAY_ORDER),
                ])->columns(2),

            Section::make(__('admin.achievement_criteria'))
                ->description(__('admin.achievement_criteria_description'))
                ->columns(2)
                ->schema([
                    Select::make('criteria_type')
                        ->label(__('admin.criteria_type'))
                        ->options(array_combine(Achievement::CRITERIA_LIST, array_map(
                            fn($k) => __("admin.criteria_{$k}"),
                            Achievement::CRITERIA_LIST
                        )))
                        ->required()
                        ->native(false)
                        // Disable on edit — changing criteria invalidates existing awards
                        ->disabled(fn($record) => $record !== null),

                    TextInput::make('criteria_value')
                        ->label(__('admin.criteria_value'))
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->disabled(fn($record) => $record !== null),

                    TextInput::make('points_rewards')
                        ->label(__('admin.points_rewards'))
                        ->helperText(__('admin.points_rewards_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->default(Achievement::DEFAULT_POINTS_REWARDS),
                ]),
        ]);
    }
}
