<?php

use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\RequestResponse;
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
            $table->foreignIdFor(BloodRequest::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(Donor::class)
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedTinyInteger('status')->default(RequestResponse::DEFAULT_STATUS)->index();
            $table->timestamp('responded_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->string('verification_qr_code')->nullable()->unique();
            $table->timestamp('qr_code_expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignIdFor(Appointment::class)->nullable()->constrained()->nullOnDelete();
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
