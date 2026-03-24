<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Enums\BloodRequestStatus;
use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use App\Jobs\CancelExcessResponsesJob;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

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
        $unitsIncreased = $record->units_needed > ($this->originalValues['units_needed'] ?? $record->units_needed);

        if ($criticalChanged && $isBroadcasted) {
            try {
                CancelExcessResponsesJob::dispatchSync($record);

                $record->status = BloodRequestStatus::PENDING;
                $record->save();

                $count = $record->broadcastToEligibleDonors();

                Log::info('Blood request re-broadcast after critical field edit', [
                    'blood_request_id' => $record->id,
                    'changed_fields' => $this->getChangedCriticalFields(),
                    'donors_notified' => $count,
                ]);

                $notificationStatus = $count > 0
                    ? __('organization.broadcast_resent_with_count', ['count' => $count])
                    : __('organization.broadcast_resent_no_donors');

                Notification::make()
                    ->title($notificationStatus)
                    ->body(__('organization.previous_donor_responses_cancelled'))
                    ->warning()
                    ->duration(8000)
                    ->send();
            } catch (\Exception $e) {
                Log::error('Failed to re-broadcast blood request after edit', [
                    'blood_request_id' => $record->id,
                    'error' => $e->getMessage(),
                ]);

                Notification::make()
                    ->danger()
                    ->title(__('organization.error_in_resending_broadcast'))
                    ->body(__('organization.changes_saved_but_rebroadcast_failed'))
                    ->send();
            }

            return;
        }

        if ($unitsIncreased && $isBroadcasted) {
            try {
                $count = $record->broadcastToEligibleDonors();

                Log::info('Blood request top-up broadcast after units_needed increase', [
                    'blood_request_id' => $record->id,
                    'old_units' => $this->originalValues['units_needed'],
                    'new_units' => $record->units_needed,
                    'donors_notified' => $count,
                ]);

                $notificationStatus = $count > 0
                    ? __('organization.broadcast_expanded_with_count', ['count' => $count])
                    : __('organization.no_additional_donors_found');

                Notification::make()
                    ->title($notificationStatus)
                    ->success()
                    ->duration(6000)
                    ->send();
            } catch (\Exception $e) {
                Log::error('Failed to top-up broadcast blood request after units edit', [
                    'blood_request_id' => $record->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return;
        }
    }

    private function criticalFieldChanged(): bool
    {
        foreach (self::CRITICAL_FIELDS as $field) {
            if (in_array($field, ['lat', 'lng'], true)) {
                // Compare coordinates as floats because map values can differ slightly in precision.
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
