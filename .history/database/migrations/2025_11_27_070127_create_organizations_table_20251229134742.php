<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Governorate;
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
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Governorate::class)->nullable()->constrained()->nullOnDelete();
            $table->string('org_name')->index();
            $table->text('description')->nullable();
            $table->string('license_number')->unique()->index();
            $table->string('license_document_path')->nullable();
            $table->string('responsible_person_name');
            $table->string('responsible_person_position')->nullable();
            $table->string('responsible_person_email')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('street_address')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->json('working_days')->nullable();
            $table->unsignedInteger('daily_capacity')->default(Organization::DEFAULT_DAILY_CAPACITY);
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('total_request_created')->default(Organization::DEFAULT_TOTAL_REQUEST_CREATED);
            $table->unsignedInteger('total_donation_verified')->default(Organization::DEFAULT_TOTAL_DONATION_VERIFIED);
            $table->enum('approval_status', Organization::STATUS_LIST)->default(Organization::DEFAULT_APPROVAL_STATUS)->index();
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users')->nullOnDelete();
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
