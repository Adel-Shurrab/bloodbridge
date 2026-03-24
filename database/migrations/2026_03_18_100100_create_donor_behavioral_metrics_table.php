<?php

use App\Models\Donor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donor_behavioral_metrics', function (Blueprint $table) {
            $table->foreignIdFor(Donor::class)
                ->primary()
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('response_count_30d')->default(0)->comment('Number of blood request responses in last 30 days');
            $table->float('acceptance_rate')->default(0.5)->comment('% of requests ACCEPTED / (ACCEPTED + IGNORED + DECLINED)');
            $table->float('ignore_rate')->default(0.25)->comment('% of requests IGNORED');
            $table->float('decline_rate')->default(0.25)->comment('% of requests DECLINED');
            $table->float('no_show_rate')->default(0)->comment('% of scheduled appointments NOT SHOWED');
            $table->integer('avg_response_time_minutes')->default(0)->comment('Median minutes between broadcast and response');
            $table->date('last_response_date')->nullable()->comment('When donor last responded');
            $table->integer('total_donations_lifetime')->default(0)->comment('Total COMPLETED donations');
            $table->json('time_of_day_preference')->nullable()->comment('JSON distribution {morning: 0.3, afternoon: 0.4, evening: 0.3}');
            $table->json('day_of_week_preference')->nullable()->comment('JSON distribution across 7 days');
            $table->timestamp('computed_at')->nullable()->comment('Timestamp of last computation');
            $table->timestamps();
            
            // Indexes
            $table->index('last_response_date');
            $table->index('computed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_behavioral_metrics');
    }
};
