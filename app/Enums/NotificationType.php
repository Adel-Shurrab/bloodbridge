<?php

namespace App\Enums;

/**
 * Notification Type Constants
 * 
 * Centralized notification type definitions for logging and tracking.
 * Use these enums instead of magic strings for consistency and type safety.
 * 
 * Usage:
 *   Log::info('Sending notification', ['type' => NotificationType::BLOOD_REQUEST_MATCH->value]);
 */
enum NotificationType: string
{
    case BLOOD_REQUEST_MATCH = 'blood_request_match';
    case DONOR_RESPONSE = 'donor_response';
    case RESPONSE_NOT_NEEDED = 'response_not_needed';
    case DONOR_INELIGIBILITY = 'donor_ineligibility';
    case SYSTEM_ANNOUNCEMENT = 'system_announcement';

    /**
     * Get human-readable label for the notification type
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::BLOOD_REQUEST_MATCH => __('Blood Request Match'),
            self::DONOR_RESPONSE => __('Donor Response'),
            self::RESPONSE_NOT_NEEDED => __('Response Not Needed'),
            self::DONOR_INELIGIBILITY => __('Donor Ineligibility'),
            self::SYSTEM_ANNOUNCEMENT => __('System Announcement'),
        };
    }

    /**
     * Get the notification class name for this type
     */
    public function getNotificationClass(): string
    {
        return match ($this) {
            self::BLOOD_REQUEST_MATCH => 'App\\Notifications\\BloodRequestMatchNotification',
            self::DONOR_RESPONSE => 'App\\Notifications\\DonorResponseNotification',
            self::RESPONSE_NOT_NEEDED => 'App\\Notifications\\ResponseNotNeededNotification',
            self::DONOR_INELIGIBILITY => 'App\\Notifications\\DonorIneligibilityNotification',
            self::SYSTEM_ANNOUNCEMENT => 'App\\Notifications\\SystemAnnouncement',
        };
    }
}
