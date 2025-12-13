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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->index();
            $table->string('national_id', 20)->unique()->index()->comment('National/ID number');
            $table->enum('gender', ['male', 'female'])->index();
            $table->date('birth_date')->nullable()->index();
            $table->enum('blood_type', ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])->nullable()->comment('Self declared');
            $table->enum('verified_blood_type', ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])->nullable()->comment('Hospital verified');
            $table->decimal('lat', 10, 7)->nullable()->comment('Latitude for location');
            $table->decimal('lng', 10, 7)->nullable()->comment('Longitude for location');
            $table->string('city', 255)->nullable()->index()->comment('City of residence');
            $table->unsignedInteger('points')->default(0)->comment('Loyalty/reward points');
            $table->unsignedInteger('level')->default(1)->comment('Donor level/tier');
            $table->unsignedInteger('total_donations')->default(0)->comment('Total donation count');
            $table->timestamp('last_donated_at')->nullable()->index()->comment('Last donation timestamp');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['gender', 'blood_type']);
            $table->index(['is_active' => 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
