<?php

namespace App\DataTransferObjects;

final readonly class ScoringResult
{
    /**
     * @param int    $donorId     The donor's ID
     * @param float  $score       Acceptance probability (0.0 to 1.0)
     * @param bool   $isColdStart True if donor has insufficient history
     * @param string $source      Where the score came from
     *                            'db_cache' | 'fastapi' | 'rule_based' | 'cold_start' | 'neutral'
     */
    public function __construct(
        public readonly int    $donorId,
        public readonly float  $score,
        public readonly bool   $isColdStart,
        public readonly string $source,
    ) {}

    /**
     * Create a cold start result for donors with insufficient history.
     * These donors go to the exploration bucket in epsilon-greedy.
     */
    public static function coldStart(int $donorId): self
    {
        return new self(
            donorId: $donorId,
            score: 0.5,   // Neutral score
            isColdStart: true,
            source: 'cold_start',
        );
    }

    /**
     * Create a neutral result when all scoring methods fail.
     * This is the last resort — ensures the system never crashes.
     */
    public static function neutral(int $donorId, string $source = 'neutral'): self
    {
        return new self(
            donorId: $donorId,
            score: 0.5,
            isColdStart: false,
            source: $source,
        );
    }

    /**
     * Create a scored result from any scoring method.
     * Score is clamped between 0.0 and 1.0 to prevent invalid values.
     */
    public static function fromModel(int $donorId, float $score, string $source): self
    {
        return new self(
            donorId: $donorId,
            score: max(0.0, min(1.0, $score)), // Always between 0 and 1
            isColdStart: false,
            source: $source,
        );
    }
}
