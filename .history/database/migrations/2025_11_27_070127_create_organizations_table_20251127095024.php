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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->string('org_name')->index();
            $table->string('license_number')->unique()->index();
            $table->string('license_document_path')->nullable();
            $table->string('responsible_person_name');
            $table->string('responsible_person_position')->nullable();
            $table->string('responsible_person_email')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('city')->nullable()->index();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->string('working_days')->nullable();
            $table->unsignedInteger('daily_capacity')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('total_request_created')->default(0);
            $table->unsignedInteger('total_donation_verified')->default(0);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
