<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class OrganizationForm
{
    /**
     * Configure the organization form schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('User Account'))
                    ->description(__('Create New User Account or Select Existing'))
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Select::make('user_id')
                            ->label(__('Linked Account'))
                            ->relationship('user', 'name', function ($query) {
                                return $query->where('role', UserRole::ORGANIZATION)
                                    ->orderByRaw('EXISTS (SELECT 1 FROM organizations WHERE organizations.user_id = users.id) ASC')
                                    ->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(function (User $user, $record) {
                                $isOccupied = Organization::where('user_id', $user->id)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                                return $isOccupied ? $user->name . ' (' . __('Occupied') . ')' : $user->name;
                            })
                            ->disableOptionWhen(function (string $value, $record) {
                                return Organization::where('user_id', $value)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'email'),

                                TextInput::make('phone')
                                    ->label(__('Phone'))
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'phone'),

                                TextInput::make('password')
                                    ->label(__('Password'))
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->minLength(8)
                                    ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                                    ->hidden(fn(string $context) => $context === 'edit'),
                            ])
                            ->createOptionAction(function ($action) {
                                return $action
                                    ->modalHeading(__('Create New User'))
                                    ->modalSubmitActionLabel(__('Create'));
                            }),
                    ])->columns(2),

                Section::make(__('Organization Information'))
                    ->description(__('Basic information and license'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('org_name')
                            ->label(__('Organization Name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('license_number')
                            ->label(__('License Number'))
                            ->unique('organizations', 'license_number', ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('license_document_path')
                            ->label(__('License Document'))
                            ->image()
                            ->disk('public')
                            ->directory('organization-licenses')
                            ->visibility('public')
                            ->openable()
                            ->downloadable(),

                        TextInput::make('description')
                            ->label(__('Organization Description'))
                            ->columnSpanFull(),

                        TextInput::make('user_name')
                            ->label(__('Manager Name'))
                            ->formatStateUsing(fn($record) => $record?->user?->name)
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->hidden(fn($operation) => $operation === 'create'),

                        TextInput::make('responsible_person_position')
                            ->label(__('Manager Position'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('user_email')
                            ->label(__('Manager Email'))
                            ->formatStateUsing(fn($record) => $record?->user?->email)
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->hidden(fn($operation) => $operation === 'create'),
                    ])->columns(2),

                Section::make(__('Public Contact Information'))
                    ->description(__('How the public can contact the organization'))
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label(__('Contact Email'))
                            ->email()
                            ->unique('organizations', 'contact_email', ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('contact_phone')
                            ->label(__('Contact Phone'))
                            ->tel()
                            ->unique('organizations', 'contact_phone', ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make(__('Location and Opening Hours'))
                    ->description(__('Geographic location details and operating hours'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('governorate_id')
                                    ->label(__('Governorate'))
                                    ->relationship('governorate', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TimePicker::make('opening_time')
                                    ->label(__('Opening Time')),

                                TimePicker::make('closing_time')
                                    ->label(__('Closing Time')),

                                Placeholder::make('24_7_indicator')
                                    ->label(__('Working Hours'))
                                    ->content(__('Operates 24/7'))
                                    ->visible(fn($record) => $record && $record->opening_time === null && $record->closing_time === null),
                            ]),

                        CheckboxList::make('working_days')
                            ->label(__('Working Days'))
                            ->options(Organization::getWorkingDaysOptions())
                            ->columns(4)
                            ->required()
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn($state) => is_array($state) ? array_map('intval', array_values($state)) : [])
                            ->columnSpanFull()
                            ->hidden(fn($operation) => $operation === 'view'),

                        Placeholder::make('working_days_display')
                            ->label(__('Working Days'))
                            ->content(function ($record) {
                                if (! $record || ! $record->working_days) {
                                    return '-';
                                }

                                $days = Organization::getWorkingDaysOptions();

                                return collect($record->working_days)
                                    ->map(fn($day) => $days[$day] ?? $day)
                                    ->implode(', ');
                            })
                            ->columnSpanFull()
                            ->visible(fn($operation) => $operation === 'view'),

                        TextInput::make('daily_capacity')
                            ->label(__('Daily Capacity'))
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])->columns(2),
            ]);
    }
}
