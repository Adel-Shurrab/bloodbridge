<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Enums\NotificationType;
use Throwable;

class NotificationService
{

    // Single user 
    public function send(
        mixed $notifiable,
        object $notification,
        ?NotificationType $type = null,
    ): array {
        try {
            $notifiableType = get_class($notifiable);
            $notificationClass = get_class($notification);
            $notifiableId = $notifiable->getKey();

            // Get the recipient's preferred locale if available, otherwise use current locale
            $recipientLocale = null;
            if (method_exists($notifiable, 'getLocale')) {
                $recipientLocale = $notifiable->getLocale();
            } elseif (property_exists($notifiable, 'locale')) {
                $recipientLocale = $notifiable->locale;
            }

            // If we found a locale preference, temporarily set it for notification building
            $previousLocale = null;
            if ($recipientLocale) {
                $previousLocale = app()->getLocale();
                app()->setLocale($recipientLocale);
            }

            try {
                // Send the notification
                $notifiable->notify($notification);
            } finally {
                // Restore previous locale
                if ($previousLocale) {
                    app()->setLocale($previousLocale);
                }
            }

            // Log success
            Log::info('Notification sent successfully', [
                'notifiable_type' => class_basename($notifiableType),
                'notifiable_id' => $notifiableId,
                'notification_type' => class_basename($notificationClass),
                'notification_class' => $notificationClass,
                'enum_type' => $type?->value,
                'recipient_locale' => $recipientLocale ?? app()->getLocale(),
            ]);

            return [
                'success' => true,
                'error' => null,
                'notifiable_id' => $notifiableId,
            ];
        } catch (Throwable $e) {
            $this->logFailure($notifiable, $notification, $e, $type);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'notifiable_id' => $notifiable->getKey(),
            ];
        }
    }

    /**
     * Send notifications to multiple notifiables
     */
    public function sendBatch(
        iterable $notifiables,
        object $notification,
        ?NotificationType $type = null,
    ): array {
        $results = [
            'success' => 0,
            'failed' => 0,
            'total' => 0,
            'failures' => [],
        ];

        foreach ($notifiables as $notifiable) {
            $results['total']++;
            $result = $this->send($notifiable, $notification, $type);

            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['failures'][$result['notifiable_id']] = $result['error'];
            }
        }

        // Log batch summary
        Log::info('Batch notification completed', [
            'notification_class' => get_class($notification),
            'notification_type' => class_basename(get_class($notification)),
            'enum_type' => $type?->value,
            'success' => $results['success'],
            'failed' => $results['failed'],
            'total' => $results['total'],
            'failure_count' => count($results['failures']),
        ]);

        // Log individual failures if any
        if (!empty($results['failures'])) {
            foreach ($results['failures'] as $id => $error) {
                Log::warning('Batch notification failed for notifiable', [
                    'notifiable_id' => $id,
                    'error' => $error,
                    'notification_class' => get_class($notification),
                ]);
            }
        }

        return $results;
    }

    private function logFailure(
        mixed $notifiable,
        object $notification,
        Throwable $exception,
        ?NotificationType $type = null,
    ): void {
        Log::error('Notification failed', [
            'notifiable_type' => class_basename(get_class($notifiable)),
            'notifiable_id' => $notifiable->getKey(),
            'notification_type' => class_basename(get_class($notification)),
            'notification_class' => get_class($notification),
            'enum_type' => $type?->value,
            'error' => $exception->getMessage(),
            'error_class' => get_class($exception),
            'error_code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    // With retry mechanism 
    public function sendWithRetry(
        mixed $notifiable,
        object $notification,
        int $maxRetries = 3,
        int $delayMs = 100,
        ?NotificationType $type = null,
    ): array {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $result = $this->send($notifiable, $notification, $type);

            if ($result['success']) {
                if ($attempt > 1) {
                    Log::info('Notification succeeded after retry', [
                        'notifiable_id' => $notifiable->getKey(),
                        'notification_class' => get_class($notification),
                        'attempts' => $attempt,
                    ]);
                }

                return [
                    'success' => true,
                    'attempts' => $attempt,
                    'error' => null,
                ];
            }

            // Wait before retry (don't retry on last attempt)
            if ($attempt < $maxRetries) {
                usleep($delayMs * 1000);
            }
        }

        // All retries failed
        Log::error('Notification failed after all retries', [
            'notifiable_id' => $notifiable->getKey(),
            'notification_class' => get_class($notification),
            'max_attempts' => $maxRetries,
            'error' => $result['error'] ?? 'Unknown error',
        ]);

        return [
            'success' => false,
            'attempts' => $maxRetries,
            'error' => $result['error'] ?? 'Failed after ' . $maxRetries . ' attempts',
        ];
    }
}
