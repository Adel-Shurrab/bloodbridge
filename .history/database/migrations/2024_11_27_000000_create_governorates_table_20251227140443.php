<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->timestamps();
        });

        // The following lines seem to be an attempt to add columns to a 'donors' table,
        // but they are incorrectly placed within the 'governorates' migration.
        // To correctly add these columns, you should create a new migration for the 'donors' table
        // or modify an existing 'donors' migration.
        //
        // If the intention was to add these columns to the 'governorates' table itself,
        // this would be syntactically incorrect as these are not valid column definitions
        // within the Schema::create callback for 'governorates'.
        //
        // Given the instruction "update donors migration to use governorate_id",
        // this code snippet is likely intended for a *different* migration file (e.g., create_donors_table).
        //
        // For now, I will place the provided code snippet as literally as possible
        // where it seems to be intended to be inserted, which is after the governorates table creation
        // and before the data seeding loop. This will result in a syntactically incorrect file
        // if interpreted as PHP code within this context.
        //
        // If you intended to add a 'governorate_id' to the 'donors' table,
        // please provide the 'donors' migration file for editing.

        // Start of the problematic snippet from the instruction
        // This part seems to be a mix of schema definition and data seeding.
        // It's placed here based on the provided "Code Edit" snippet's context.
        // This will likely cause a syntax error or unexpected behavior.
        // Please review this section.
        //
        // $table->foreignId('governorate_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        // $table->string('national_id', 9)->unique()->index()->comment('National/ID number');
        // $table->enum('gender', Donor::GENDER_LIST)->index();
        // $table->date('birth_date')->nullable()->index();
        // $table->decimal('lat', 10, 7)->nullable()->comment('Latitude for location');
        // $table->decimal('lng', 10, 7)->nullable()->comment('Longitude for location');
        // $table->unsignedInteger('points')->default(0)->comment('Loyalty/reward points');
        // End of the problematic snippet from the instruction

        $governorates = ['غزة', 'خانيونس', 'شمال غزة', 'دير البلح', 'رفح'];
        foreach ($governorates as $name) {
            DB::table('governorates')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};
