<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Drop the donor_behavioral_metrics table.
 *
 * The table was created as a pre-computed feature cache for the FastAPI scoring
 * service but was never populated by any writer. The FastAPI service queries
 * request_responses directly at scoring time, making this table redundant.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('donor_behavioral_metrics');
    }

    public function down(): void
    {
        // Intentionally not recreated — use git to restore the original migration
        // if a rollback is truly needed.
    }
};
