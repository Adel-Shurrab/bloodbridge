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
                Section::make('محتوى الإعلان')
                    ->description('أدخل محتوى الرسالة التي سيتم إرسالها.')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان الإعلان')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn($record) => $record?->status === 1),
                        RichEditor::make('body')
                            ->label('نص الإعلان (الرسالة)')
                            ->required()
                            ->disabled(fn($record) => $record?->status === 1),
                    ]),

                Section::make('إعدادات الإرسال')
                    ->description('حدد الفئة المستهدفة للإعلان.')
                    ->schema([
                        Select::make('target_type')
                            ->label('الجمهور المستهدف')
                            ->options([
                                'all' => 'جميع المستخدمين (النشطين والموثقين)',
                                'role' => 'دور محدد',
                                'specific_users' => 'مستخدمين محددين'
                            ])
                            ->required()
                            ->live()
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('target_role')
                            ->label('الدور المستهدف')
                            ->options([
                                'App\Models\Donor' => 'المتبرعين',
                                'App\Models\Organization' => 'المنظمات'
                            ])
                            ->required(fn($get) => $get('target_type') === 'role')
                            ->visible(fn($get) => $get('target_type') === 'role')
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('targeted_users_ids')
                            ->label('تحديد المستخدمين')
                            ->multiple()
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => User::whereNotNull('email_verified_at')->where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                            ->getOptionLabelsUsing(fn(array $values) => User::whereIn('id', $values)->pluck('name', 'id'))
                            ->required(fn($get) => $get('target_type') === 'specific_users')
                            ->visible(fn($get) => $get('target_type') === 'specific_users')
                            ->disabled(fn($record) => $record?->status === 1)
                            ->columnSpanFull(),

                        Toggle::make('send_via_email')
                            ->label('إرسال نسخة عبر البريد الإلكتروني أيضاً')
                            ->default(false)
                            ->disabled(fn($record) => $record?->status === 1),

                        Select::make('status')
                            ->label('حالة النشر')
                            ->options([
                                0 => 'مسودة',
                                1 => 'منشور (سيتم الإرسال فوراً ولن يمكن التعديل)'
                            ])
                            ->default(0)
                            ->required()
                            ->disabled(fn($record) => $record?->status === 1),
                    ])->columns(2),
            ]);
    }
}
