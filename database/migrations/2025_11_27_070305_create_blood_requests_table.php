<?php

use App\Models\Organization;
use App\Models\BloodRequest;
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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedTinyInteger('blood_type')->index();
            $table->unsignedInteger('units_needed');
            $table->unsignedTinyInteger('urgency_level')
                ->default(BloodRequest::DEFAULT_URGENCY_LEVEL)
                ->index();
            $table->json('additional_notes')->nullable();
            $table->unsignedInteger('search_radius_km')->default(10);
            $table->integer('actual_search_radius_km')->nullable()
                ->comment('Final radius used after expansion');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->unsignedTinyInteger('status')
                ->default(BloodRequest::DEFAULT_STATUS)
                ->index();
            $table->timestamp('broadcasted_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
