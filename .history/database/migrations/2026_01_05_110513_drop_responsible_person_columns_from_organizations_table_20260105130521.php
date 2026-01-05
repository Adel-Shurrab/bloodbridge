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
            $table->dropColumn([
                'responsible_person_name',
                'responsible_person_position',
                'responsible_person_email',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('responsible_person_name')->after('license_document_path');
            $table->string('responsible_person_position')->nullable()->after('responsible_person_name');
            $table->string('responsible_person_email')->nullable()->after('responsible_person_position');
        });
    }
};
