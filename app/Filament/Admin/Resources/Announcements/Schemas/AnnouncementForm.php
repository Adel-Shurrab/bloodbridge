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
                Section::make(__('Announcement Content'))
                    ->description(__('Enter the message content to be sent.'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Announcement Title'))
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn($record) => $record?->status === 1),
                        RichEditor::make('body')
                            ->label(__('Announcement Body (Message)'))
                            ->required()
                            ->disabled(fn($record) => $record?->status === 1),
                    ]),

                Section::make(__('Sending Settings'))
                    ->description(__('Select target audience for the announcement.'))
                    ->schema([
                        Select::make('target_type')
                            ->label(__('Target Audience'))
                            ->options([
                                'all' => __('All Users (Active and Verified)'),
                                'role' => __('Specific Role'),
                                'specific_users' => __('Specific Users')
                            ])
                            ->required()
                            ->live()
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('target_role')
                            ->label(__('Target Role'))
                            ->options([
                                'App\Models\Donor' => __('Donors'),
                                'App\Models\Organization' => __('Organizations')
                            ])
                            ->required(fn($get) => $get('target_type') === 'role')
                            ->visible(fn($get) => $get('target_type') === 'role')
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('targeted_users_ids')
                            ->label(__('Select Users'))
                            ->multiple()
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => User::whereNotNull('email_verified_at')->where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                            ->getOptionLabelsUsing(fn(array $values) => User::whereIn('id', $values)->pluck('name', 'id'))
                            ->required(fn($get) => $get('target_type') === 'specific_users')
                            ->visible(fn($get) => $get('target_type') === 'specific_users')
                            ->disabled(fn($record) => $record?->status === 1)
                            ->columnSpanFull(),

                        Toggle::make('send_via_email')
                            ->label(__('Send Copy Via Email Also'))
                            ->default(false)
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('status')
                            ->label(__('Publish Status'))
                            ->options([
                                0 => __('Draft'),
                                1 => __('Published (Sent immediately and cannot be edited)')
                            ])
                            ->default(0)
                            ->required()
                            ->disabled(fn($record) => $record?->status === 1),
                    ])->columns(2),
            ]);
    }
}
