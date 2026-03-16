<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Enums\BloodRequestStatus;
use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use App\Jobs\CancelExcessResponsesJob;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

use Illuminate\Support\Facades\Log;

class EditBloodRequest extends EditRecord
{
    use Translatable;

    protected static string $resource = BloodRequestResource::class;

    private const CRITICAL_FIELDS = [
        'blood_type',
        'urgency_level',
        'lat',
        'lng',
        'search_radius_km',
    ];

    /** @var array<string, mixed> */
    protected array $originalValues = [];

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $tracked = array_merge(self::CRITICAL_FIELDS, ['units_needed']);

        foreach ($tracked as $field) {
            $this->originalValues[$field] = $this->record->getOriginal($field);
        }
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $isBroadcasted = $record->status === BloodRequestStatus::BROADCASTED;

        $criticalChanged = $this->criticalFieldChanged();
        $unitsIncreased  = $record->units_needed > ($this->originalValues['units_needed'] ?? $record->units_needed);

        if ($criticalChanged && $isBroadcasted) {
            try {
                CancelExcessResponsesJob::dispatchSync($record);

                $record->status = BloodRequestStatus::PENDING;
                $record->save();

                $count = $record->broadcastToEligibleDonors();

                Log::info('Blood request re-broadcast after critical field edit', [
                    'blood_request_id' => $record->id,
                    'changed_fields'   => $this->getChangedCriticalFields(),
                    'donors_notified'  => $count,
                ]);

                $notificationStatus = $count > 0
                    ? __("Broadcast resent — :count potential donors", ['count' => $count])
                    : __('Broadcast resent — No donors found in current range');

                Notification::make()
                    ->title($notificationStatus)
                    ->body(__('Previous donor responses cancelled and notified.'))
                    ->warning()
                    ->duration(8000)
                    ->send();
            } catch (\Exception $e) {
                Log::error('Failed to re-broadcast blood request after edit', [
                    'blood_request_id' => $record->id,
                    'error'            => $e->getMessage(),
                ]);

                Notification::make()
                    ->danger()
                    ->title(__('Error in resending broadcast'))
                    ->body(__('Changes saved but error occurred while resending broadcast.'))
                    ->danger()
                    ->send();
            }

            return;
        }

        if ($unitsIncreased && $isBroadcasted) {
            try {
                $count = $record->broadcastToEligibleDonors();

                Log::info('Blood request top-up broadcast after units_needed increase', [
                    'blood_request_id' => $record->id,
                    'old_units'        => $this->originalValues['units_needed'],
                    'new_units'        => $record->units_needed,
                    'donors_notified'  => $count,
                ]);

                $notificationStatus = $count > 0
                    ? __("Broadcast expanded — :count additional donors", ['count' => $count])
                    : __('No additional donors found in current range');

                Notification::make()
                    ->title($notificationStatus)
                    ->success()
                    ->duration(6000)
                    ->send();
            } catch (\Exception $e) {
                Log::error('Failed to top-up broadcast blood request after units edit', [
                    'blood_request_id' => $record->id,
                    'error'            => $e->getMessage(),
                ]);
            }

            return;
        }
    }

    private function criticalFieldChanged(): bool
    {
        foreach (self::CRITICAL_FIELDS as $field) {
            if (in_array($field, ['lat', 'lng'], true)) {
                // Compare coordinates as floats — the map picker can return
                // slightly different decimal precision than what's stored in DB.
                if (round((float) $this->originalValues[$field], 6) !== round((float) $this->record->{$field}, 6)) {
                    return true;
                }
                continue;
            }

            $original = (string) ($this->originalValues[$field] instanceof \BackedEnum
                ? $this->originalValues[$field]->value
                : $this->originalValues[$field]);

            $current = (string) ($this->record->{$field} instanceof \BackedEnum
                ? $this->record->{$field}->value
                : $this->record->{$field});

            if ($original !== $current) {
                return true;
            }
        }

        return false;
    }

    private function getChangedCriticalFields(): array
    {
        $changed = [];

        foreach (self::CRITICAL_FIELDS as $field) {
            if (in_array($field, ['lat', 'lng'], true)) {
                if (round((float) $this->originalValues[$field], 6) !== round((float) $this->record->{$field}, 6)) {
                    $changed[] = $field;
                }
                continue;
            }

            $original = (string) ($this->originalValues[$field] instanceof \BackedEnum
                ? $this->originalValues[$field]->value
                : $this->originalValues[$field]);

            $current = (string) ($this->record->{$field} instanceof \BackedEnum
                ? $this->record->{$field}->value
                : $this->record->{$field});

            if ($original !== $current) {
                $changed[] = $field;
            }
        }

        return $changed;
    }
}

