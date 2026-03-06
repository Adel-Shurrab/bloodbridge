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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('target_type', ['all', 'role', 'specific_users']);
            $table->string('target_role')->nullable();
            $table->json('targeted_users_ids')->nullable();
            $table->boolean('send_via_email')->default(false);
            $table->tinyInteger('status')->default(0)->comment('0: draft, 1: published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
