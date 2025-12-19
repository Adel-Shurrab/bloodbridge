<?php

use App\Models\User;
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
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onDelete('cascade');
            $table->string('national_id', 9)->unique()->index()->comment('National/ID number');
            $table->enum('gender', ['male', 'female'])->index();
            $table->date('birth_date')->nullable()->index();
            $table->decimal('lat', 10, 7)->nullable()->comment('Latitude for location');
            $table->decimal('lng', 10, 7)->nullable()->comment('Longitude for location');
            $table->string('city', 255)->nullable()->index()->comment('City of residence');
            $table->unsignedInteger('points')->default(0)->comment('Loyalty/reward points');
            $table->unsignedInteger('level')->default(1)->comment('Donor level/tier');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['gender']);
            $table->index(['user_id', 'national_id']);
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
