<?php

namespace App\Filament\Resources\Donors;

use App\Filament\Resources\Donors\Pages\CreateDonor;
use App\Filament\Resources\Donors\Pages\EditDonor;
use App\Filament\Resources\Donors\Pages\ListDonors;
use App\Filament\Resources\Donors\Schemas\DonorForm;
use App\Filament\Resources\Donors\Tables\DonorsTable;
use App\Models\Donor;
use BackedEnum;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonorResource extends Resource
{
    protected static ?string $model = Donor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'المتبرعين';

    protected static ?string $modelLabel = 'متبرع';

    protected static ?string $pluralModelLabel = 'المتبرعين';

    protected static ?string $recordTitleAttribute = 'national_id';


    public static function form(Schema $schema): Schema
    {
        return DonorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonors::route('/'),
            'create' => CreateDonor::route('/create'),
            'edit' => EditDonor::route('/{record}/edit'),
        ];
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('المعلومات الشخصية')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('user.name')
                            ->label('الاسم الكامل')
                            ->icon('heroicon-m-user'),
                        \Filament\Infolists\Components\TextEntry::make('national_id')
                            ->label('رقم الهوية')
                            ->icon('heroicon-m-identification')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('gender')
                            ->label('الجنس')
                            ->formatStateUsing(fn($state) => \App\Models\Donor::getGenderOptions()[$state] ?? $state),
                        \Filament\Infolists\Components\TextEntry::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->date('Y/m/d'),
                        \Filament\Infolists\Components\TextEntry::make('user.phone')
                            ->label('رقم الهاتف')
                            ->icon('heroicon-m-phone')
                            ->url(fn($record) => 'tel:' . $record->user->phone),
                        \Filament\Infolists\Components\TextEntry::make('user.email')
                            ->label('البريد الإلكتروني')
                            ->icon('heroicon-m-envelope'),
                    ])
                    ->columns(3),

                \Filament\Infolists\Components\Section::make('الملف الصحي')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('healthProfile.blood_type')
                            ->label('فصيلة الدم')
                            ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                \App\Models\Donor::BLOOD_TYPE_O_POSITIVE, \App\Models\Donor::BLOOD_TYPE_O_NEGATIVE => 'danger',
                                default => 'primary',
                            }),
                        \Filament\Infolists\Components\TextEntry::make('healthProfile.weight')
                            ->label('الوزن')
                            ->suffix(' كجم'),
                        \Filament\Infolists\Components\TextEntry::make('healthProfile.height')
                            ->label('الطول')
                            ->suffix(' سم'),
                        \Filament\Infolists\Components\TextEntry::make('healthProfile.total_donations')
                            ->label('عدد التبرعات السابقة'),
                        \Filament\Infolists\Components\IconEntry::make('healthProfile.is_eligible')
                            ->label('مؤهل للتبرع حالياً')
                            ->boolean(),
                    ])
                    ->columns(3),

                \Filament\Infolists\Components\Section::make('الموقع والعنوان')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('governorate.name')
                            ->label('المحافظة'),
                        \Filament\Infolists\Components\TextEntry::make('lat')
                            ->label('إحداثيات الموقع')
                            ->formatStateUsing(fn($record) => $record->lat . ', ' . $record->lng)
                            ->url(fn($record) => "https://www.google.com/maps/search/?api=1&query={$record->lat},{$record->lng}", true)
                            ->icon('heroicon-m-map-pin')
                            ->columnSpan(2),
                    ])
                    ->columns(3),

                \Filament\Infolists\Components\Section::make('بيانات النظام')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('level')
                            ->label('المستوى')
                            ->formatStateUsing(fn($state) => number_format($state)),
                        \Filament\Infolists\Components\TextEntry::make('points')
                            ->label('النقاط')
                            ->formatStateUsing(fn($state) => number_format($state)),
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ التسجيل')
                            ->dateTime()
                            ->formatStateUsing(fn($state) => $state->diffForHumans()),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
