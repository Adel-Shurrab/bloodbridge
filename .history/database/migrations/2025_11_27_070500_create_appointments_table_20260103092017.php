<?php

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Organization;
use App\Models\Appointment;
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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(Donor::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(BloodRequest::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->dateTime('appointment_date')->index();
            $table->unsignedTinyInteger('status')
                ->default(Appointment::DEFAULT_STATUS)
                ->index();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
