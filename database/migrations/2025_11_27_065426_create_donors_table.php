<?php

use App\Models\User;
use App\Models\Donor;
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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Governorate::class)->nullable()->constrained()->nullOnDelete();
            $table->string('national_id', 9)->unique()->index()->comment('National/ID number');
            $table->enum('gender', array_column(\App\Enums\Gender::cases(), 'value'))->index();
            $table->date('birth_date')->nullable()->index();
            $table->string('auto_location_address', 500)->nullable();
            $table->decimal('lat', 10, 7)->nullable()->comment('Latitude for location');
            $table->decimal('lng', 10, 7)->nullable()->comment('Longitude for location');
            $table->unsignedInteger('points')->default(0)->comment('Loyalty/reward points');
            $table->unsignedInteger('level')->default(1)->comment('Donor level/tier');
            $table->softDeletes();
            $table->timestamps();
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
