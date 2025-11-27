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
$table->foreignId('user_id')->constrained()->onDelete('cascade');
$table->string('national_id')->unique();
$table->integer('age')->nullable();
$table->enum('gender', ['male', 'female']);
$table->date('birth_date')->nullable();
$table->string('blood_type')->nullable(); // self declared
$table->string('verified_blood_type')->nullable(); // hospital verified
$table->point('location')->nullable();
$table->string('city')->nullable();
$table->integer('points')->default(0);
$table->integer('level')->default(1);
$table->integer('total_donations')->default(0);
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
