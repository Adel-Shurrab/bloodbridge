<?php

namespace App\Filament\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Resources\Resources;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Organization;
class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('org_name')
                    ->label('اسم المنظمة')
                    ->weight('bold')
                    ->searchable(),
                
                TextColumn::make('approval_status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->date('d/m/Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // زر الموافقة
                Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Organization $record) => $record->approval_status === 'pending')
                    ->action(function (Organization $record) {
                        $record->update(['approval_status' => 'approved']);
                        Notification::make()->title('تمت الموافقة')->success()->send();
                    }),

                // زر الرفض
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Organization $record) => $record->approval_status === 'pending')
                    ->action(function (Organization $record) {
                        $record->update(['approval_status' => 'rejected']);
                        Notification::make()->title('تم الرفض')->danger()->send();
                    }),
            ]);
    }
}
