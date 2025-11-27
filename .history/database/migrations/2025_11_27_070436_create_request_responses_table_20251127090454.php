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
        Schema::create('request_responses', function (Blueprint $table) {
            $table->id();
$table->foreignId('donor_id')->constrained()->onDelete('cascade');
$table->foreignId('blood_request_id')->constrained()->onDelete('cascade');
$table->enum('status', ['pending','accepted','declined','completed','ignored'])->default('pending');
$table->timestamp('responded_at')->nullable();
$table->text('decline_reason')->nullable();
$table->boolean('verification_qr_code')->default(false);
$table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
$table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_responses');
    }
};
