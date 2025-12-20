<?php

use App\Models\Organization;
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
            $table->enum('blood_type', constants('blood_types_list'))->index();
            $table->unsignedInteger('units_needed');
            $table->enum('urgency_level', constants('blood_request_urgency_level_list'))
                ->default(app_constant('blood_request.default_urgency_level'))
                ->index();
            $table->text('additional_notes')->nullable();
            $table->unsignedInteger('search_radius_km')->default(10);
            $table->enum('status', constants('blood_request_status_list'))
                ->default(app_constant('blood_request.default_status'))
                ->index();
            $table->unsignedInteger('donors_accepted')->default(0);
            $table->unsignedInteger('donors_completed')->default(0);
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
