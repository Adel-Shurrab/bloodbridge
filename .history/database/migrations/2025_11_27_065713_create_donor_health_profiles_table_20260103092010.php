<?php

use App\Models\Donor;
use App\Models\DonorHealthProfile;
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
        Schema::create('donor_health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Donor::class)
                ->constrained()
                ->onDelete('cascade');
            $table->index('donor_id');
            $table->unsignedInteger('weight')->comment('Weight in kg - Required');
            $table->unsignedInteger('height')->comment('Height in cm - Required');
            $table->boolean('chronic_disease')->default(false)->comment('Has chronic disease condition');
            $table->boolean('recent_donation')->default(false)->comment('Flag: recent donation');
            $table->boolean('infection')->default(DonorHealthProfile::DEFAULT_INFECTION)->comment('Current active infection');
            $table->boolean('is_eligible')->default(DonorHealthProfile::DEFAULT_IS_ELIGIBLE)->index()->comment('Is eligible to donate now');
            $table->boolean('has_recent_surgery')->default(DonorHealthProfile::DEFAULT_HAS_RECENT_SURGERY)->comment('Had surgery recently');
            $table->date('surgery_date')->nullable()->comment('Date of recent surgery');
            $table->date('next_eligible_date')->nullable()->index()->comment('Auto-calculated: when donor becomes eligible again');
            $table->date('last_donation_date')->nullable()->index()->comment('Date of last donation');
            $table->unsignedTinyInteger('blood_type')->nullable()->index()->comment('Self declared');
            $table->unsignedTinyInteger('verified_blood_type')->nullable()->comment('Hospital verified');
            $table->unsignedInteger('total_donations')->default(DonorHealthProfile::DEFAULT_TOTAL_DONATIONS)->comment('Total donation count');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['donor_id', 'is_eligible']);
            $table->index(['next_eligible_date', 'is_eligible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_health_profiles');
    }
};
