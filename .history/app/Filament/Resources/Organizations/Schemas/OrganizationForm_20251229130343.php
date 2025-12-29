<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /Section::make('التفاصيل')
                    ->schema([
                        Forms\Components\TextInput::make('org_name')->label('الاسم')->disabled(),
                        Forms\Components\TextInput::make('license_number')->label('الترخيص')->disabled(),
                        Forms\Components\FileUpload::make('license_document_path')
                            ->label('صورة الرخصة')
                            ->image()
                            ->disk('public')
                            ->openable()
                            ->disabled(),
                    ])
            ]);
    }
}
