<?php

use App\Models\Donor;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eligibility_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Donor::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Organization::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->unsignedTinyInteger('check_type')->index();
            $table->boolean('is_eligible')->default(true)->index();
            $table->boolean('is_permanent')
                ->default(false);
            $table->text('rejection_reason')->nullable();
            $table->json('answers_snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligibility_logs');
    }
};
