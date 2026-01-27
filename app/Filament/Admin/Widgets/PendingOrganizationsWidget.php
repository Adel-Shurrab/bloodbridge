<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Actions\ViewAction;

class PendingOrganizationsWidget extends TableWidget
{
    protected static ?string $heading = 'منظمات بانتظار الموافقة';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn() => Organization::where('approval_status', '=', \App\Enums\OrganizationStatus::PENDING, 'and')
            )
            ->modelLabel('منظمة')
            ->pluralModelLabel('منظمات')
            ->columns([
                Tables\Columns\TextColumn::make('org_name')
                    ->label('اسم المنظمة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_person_name')
                    ->label('المسؤول')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('البريد الإلكتروني'),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('رقم الهاتف'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y/m/d')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('عرض')
                    ->form(fn($form) => OrganizationResource::form($form))
                    ->modalWidth('7xl'),
                Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approval_status = \App\Enums\OrganizationStatus::APPROVED;
                        $record->save();

                        Notification::make()
                            ->title('تمت الموافقة بنجاح')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approval_status = \App\Enums\OrganizationStatus::REJECTED;
                        $record->save();

                        Notification::make()
                            ->title('تم رفض المنظمة')
                            ->danger()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('لا توجد منظمات')
            ->emptyStateDescription('لا توجد منظمات بانتظار الموافقة حالياً');
    }
}
