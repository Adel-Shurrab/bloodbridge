<?php

namespace App\Filament\Admin\Pages;

use App\Settings\ScoringSettings;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ManageScoringSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.manage-scoring-settings.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system-reports');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('filament.pages.manage-scoring-settings.title');
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return __('filament.pages.manage-scoring-settings.description');
    }

    protected static string $settings = ScoringSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Scoring Settings')
                    ->tabs([
                        Tab::make('ML Model')
                            ->label(__('admin.ml_model'))
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Section::make(__('admin.ml_scoring_system'))
                                    ->description(__('admin.ml_scoring_description'))
                                    ->schema([
                                        Toggle::make('ml_scoring_enabled')
                                            ->label(__('admin.enable_ml_scoring'))
                                            ->helperText(__('admin.enable_ml_scoring_helper'))
                                            ->live(),
                                        
                                        TextInput::make('ml_enabled_since')
                                            ->label(__('admin.ml_enabled_since'))
                                            ->disabled()
                                            ->hint(__('admin.automatically_set'))
                                            ->visible(fn($get) => $get('ml_scoring_enabled')),
                                    ])->columns(1),
                                
                                Section::make(__('admin.donor_matching_behavior'))
                                    ->visible(fn($get) => $get('ml_scoring_enabled'))
                                    ->schema([
                                        TextInput::make('max_notifications_per_broadcast')
                                            ->label(__('admin.max_notifications_per_broadcast'))
                                            ->helperText(__('admin.max_notifications_helper'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),

                                        TextInput::make('exploration_ratio')
                                            ->label(__('admin.exploration_ratio'))
                                            ->helperText(__('admin.exploration_ratio_helper'))
                                            ->numeric()
                                            ->step(0.01)
                                            ->required()
                                            ->minValue(0)
                                            ->maxValue(1),

                                        TextInput::make('min_history_for_exploitation')
                                            ->label(__('admin.min_history_for_exploitation'))
                                            ->helperText(__('admin.min_history_helper'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(0),

                                        TextInput::make('score_staleness_days')
                                            ->label(__('admin.score_staleness_days'))
                                            ->helperText(__('admin.score_staleness_helper'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),
                                    ])->columns(2),
                            ]),

                        Tab::make('A/B Testing')
                            ->label(__('admin.ab_testing'))
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                Section::make(__('admin.ab_testing_control'))
                                    ->description(__('admin.ab_testing_description'))
                                    ->schema([
                                        Toggle::make('a_b_testing_enabled')
                                            ->label(__('admin.enable_ab_testing'))
                                            ->helperText(__('admin.enable_ab_testing_helper'))
                                            ->live(),

                                        TextInput::make('a_b_test_control_percentage')
                                            ->label(__('admin.ab_test_control_percentage'))
                                            ->helperText(__('admin.ab_test_control_percentage_helper'))
                                            ->numeric()
                                            ->step(0.01)
                                            ->required()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->visible(fn($get) => $get('a_b_testing_enabled')),
                                    ])->columns(1),
                            ]),

                        Tab::make('Circuit Breaker')
                            ->label(__('admin.circuit_breaker'))
                            ->icon('heroicon-o-shield-exclamation')
                            ->schema([
                                Section::make(__('admin.circuit_breaker_settings'))
                                    ->description(__('admin.circuit_breaker_description'))
                                    ->schema([
                                        TextInput::make('circuit_breaker_failure_threshold')
                                            ->label(__('admin.failure_threshold'))
                                            ->helperText(__('admin.failure_threshold_helper'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),

                                        TextInput::make('circuit_breaker_recovery_seconds')
                                            ->label(__('admin.recovery_seconds'))
                                            ->helperText(__('admin.recovery_seconds_helper'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->suffix(__('admin.seconds')),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
