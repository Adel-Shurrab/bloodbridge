<?php

namespace App\Services;

use App\Settings\ScoringSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FastApiCircuitBreaker
{
    // Cache keys
    private const KEY_FAILURES  = 'fastapi_circuit:failures';
    private const KEY_OPENED_AT = 'fastapi_circuit:opened_at';
    private const KEY_STATE     = 'fastapi_circuit:state';

    // States
    private const STATE_CLOSED    = 'closed';    // Normal — all requests go through
    private const STATE_OPEN      = 'open';      // FastAPI is down — skip it entirely
    private const STATE_HALF_OPEN = 'half_open'; // Testing if FastAPI recovered

    public function __construct(private ScoringSettings $settings) {}

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Attempt a FastAPI call with circuit breaker protection.
     *
     * If circuit is CLOSED → try the call normally
     * If circuit is OPEN   → skip FastAPI, return null immediately
     * If circuit is HALF_OPEN → try once to see if FastAPI recovered
     *
     * @param  callable   $request  The HTTP call to attempt
     * @return mixed|null           Result or null if circuit is open
     */
    public function attempt(callable $request): mixed
    {
        $state = $this->getState();

        // Circuit is open — check if recovery time has passed
        if ($state === self::STATE_OPEN) {
            if ($this->shouldAttemptReset()) {
                $this->transitionTo(self::STATE_HALF_OPEN);
            } else {
                Log::debug('Circuit breaker OPEN — skipping FastAPI call');
                return null;
            }
        }

        // Try the request
        try {
            $result = $request();
            $this->onSuccess();
            return $result;
        } catch (\Exception $e) {
            $this->onFailure($e);
            return null;
        }
    }

    /**
     * Quick health probe.
     * Called by BroadcastService before each broadcast to check FastAPI status.
     *
     * @return bool True if FastAPI is available
     */
    public function isAvailable(): bool
    {
        // If circuit is open and recovery time hasn't passed → not available
        if ($this->getState() === self::STATE_OPEN && !$this->shouldAttemptReset()) {
            return false;
        }

        try {
            $response = Http::connectTimeout(2)
                ->timeout(3)
                ->get(config('services.fastapi.url') . '/api/health');

            $isHealthy = $response->successful()
                && $response->json('status') !== 'unhealthy';

            if ($isHealthy) {
                $this->onSuccess();
                return true;
            }

            $this->onFailure(new \Exception(
                'Health check returned: ' . $response->json('status', 'unknown')
            ));
            return false;
        } catch (\Exception $e) {
            $this->onFailure($e);
            return false;
        }
    }

    /**
     * Get current circuit state.
     */
    public function getState(): string
    {
        return Cache::store('file')->get(self::KEY_STATE, self::STATE_CLOSED);
    }

    // =========================================================================
    // Private
    // =========================================================================

    private function transitionTo(string $state): void
    {
        Cache::store('file')->put(self::KEY_STATE, $state, now()->addHours(1));
        Log::info("FastAPI circuit breaker → {$state}");
    }

    private function shouldAttemptReset(): bool
    {
        $openedAt        = Cache::store('file')->get(self::KEY_OPENED_AT);
        $recoverySeconds = $this->settings->circuit_breaker_recovery_seconds;

        if ($openedAt === null) {
            return false;
        }

        // Use now()->timestamp consistently (integer Unix timestamp)
        return (now()->timestamp - $openedAt) >= $recoverySeconds;
    }

    private function onSuccess(): void
    {
        Cache::store('file')->forget(self::KEY_FAILURES);
        Cache::store('file')->forget(self::KEY_OPENED_AT);
        $this->transitionTo(self::STATE_CLOSED);
    }

    private function onFailure(\Exception $e): void
    {
        $failures  = (int) Cache::store('file')->increment(self::KEY_FAILURES);
        $threshold = $this->settings->circuit_breaker_failure_threshold;

        Log::warning("FastAPI failure #{$failures}: " . $e->getMessage());

        if ($failures >= $threshold) {
            Cache::store('file')->put(self::KEY_OPENED_AT, now()->timestamp, now()->addHours(1));
            $this->transitionTo(self::STATE_OPEN);
            Log::error("Circuit breaker OPENED after {$failures} consecutive failures");
        }
    }
}
