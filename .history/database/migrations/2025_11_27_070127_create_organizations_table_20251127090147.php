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
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('org_name');
        $table->string('license_number')->unique();
        $table->string('license_document_path')->nullable();
        $table->string('responsible_person_name');
        $table->string('responsible_person_position')->nullable();
        $table->string('responsible_person_email')->nullable();
        $table->point('location')->nullable();
        $table->string('city')->nullable();
        $table->time('opening_time')->nullable();
        $table->time('closing_time')->nullable();
        $table->string('working_days')->nullable();
        $table->integer('daily_capacity')->default(0);
        $table->timestamp('approved_at')->nullable();
        $table->text('rejection_reason')->nullable();
        $table->integer('total_request_created')->default(0);
        $table->integer('total_donation_verified')->default(0);
        $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
