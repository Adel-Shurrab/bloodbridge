<?php

namespace App\Filament\Admin\Resources\Announcements\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use App\Models\User;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.announcement_content'))
                    ->description(__('admin.enter_the_message_content_to_be_sent'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('admin.announcement_title'))
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn($record) => $record?->status === 1),
                        RichEditor::make('body')
                            ->label(__('admin.announcement_body_message'))
                            ->required()
                            ->disabled(fn($record) => $record?->status === 1),
                    ]),

                Section::make(__('admin.sending_settings'))
                    ->description(__('admin.select_target_audience_for_the_announcement'))
                    ->schema([
                        Select::make('target_type')
                            ->label(__('admin.target_audience'))
                            ->options([
                                'all' => __('admin.all_users_active_and_verified'),
                                'role' => __('admin.specific_role'),
                                'specific_users' => __('admin.specific_users'),
                            ])
                            ->required()
                            ->live()
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('target_role')
                            ->label(__('admin.target_role'))
                            ->options([
                                'App\Models\Donor' => __('admin.donors'),
                                'App\Models\Organization' => __('admin.organizations'),
                            ])
                            ->required(fn($get) => $get('target_type') === 'role')
                            ->visible(fn($get) => $get('target_type') === 'role')
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('targeted_users_ids')
                            ->label(__('admin.select_users'))
                            ->multiple()
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => User::whereNotNull('email_verified_at')->where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                            ->getOptionLabelsUsing(fn(array $values) => User::whereIn('id', $values)->pluck('name', 'id'))
                            ->required(fn($get) => $get('target_type') === 'specific_users')
                            ->visible(fn($get) => $get('target_type') === 'specific_users')
                            ->disabled(fn($record) => $record?->status === 1)
                            ->columnSpanFull(),

                        Toggle::make('send_via_email')
                            ->label(__('admin.send_copy_via_email_also'))
                            ->default(false)
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('status')
                            ->label(__('admin.publish_status'))
                            ->options([
                                0 => __('admin.draft'),
                                1 => __('admin.published_sent_immediately_and_cannot_be_edited'),
                            ])
                            ->default(0)
                            ->required()
                            ->disabled(fn($record) => $record?->status === 1),
                    ])->columns(2),
            ]);
    }
}
