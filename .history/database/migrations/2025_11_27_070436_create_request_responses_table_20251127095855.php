<?php

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
        Schema::create('request_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_request_id')
    ->constrained('blood_requests')
    ->onDelete('cascade')
    ->name('fk_request_responses_blood_request_id');

$table->foreignId('donor_id')
    ->constrained('donors')
    ->onDelete('cascade')
    ->name('fk_request_responses_donor_id');

            $table->enum('status', ['pending', 'accepted', 'declined', 'completed', 'ignored'])->default('pending')->index();
            $table->timestamp('responded_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->boolean('verification_qr_code')->default(false);
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            // Composite unique index to prevent duplicate responses
            $table->unique(['donor_id', 'blood_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_responses');
    }
};
