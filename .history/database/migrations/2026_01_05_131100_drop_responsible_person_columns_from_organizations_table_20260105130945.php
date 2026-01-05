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
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'responsible_person_name')) {
                $table->dropColumn('responsible_person_name');
            }
            if (Schema::hasColumn('organizations', 'responsible_person_email')) {
                $table->dropColumn('responsible_person_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('responsible_person_name')->nullable();
            $table->string('responsible_person_email')->nullable();
        });
    }
};
