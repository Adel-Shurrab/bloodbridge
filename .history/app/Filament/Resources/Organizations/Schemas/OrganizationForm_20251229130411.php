<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('التفاصيل')
                    ->schema([
                        TextInput::make('org_name')->label('الاسم')->disabled(),
                        TextInput::make('license_number')->label('الترخيص')->disabled(),
                        FileUpload::make('license_document_path')
                            ->label('صورة الرخصة')
                            ->image()
                            ->disk('public')
                            ->openable()
                            ->disabled(),
                    ])
            ]);
    }
}
