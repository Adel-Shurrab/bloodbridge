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
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->string('national_id')->unique()->index();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->enum('blood_type', ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])->nullable()->comment('Self declared');
            $table->enum('verified_blood_type', ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])->nullable()->comment('Hospital verified');
            $table->point('location')->nullable();
            $table->string('city')->nullable()->index();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('level')->default(1);
            $table->unsignedInteger('total_donations')->default(0);
            $table->softDeletes();
            $table->timestamps();
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
