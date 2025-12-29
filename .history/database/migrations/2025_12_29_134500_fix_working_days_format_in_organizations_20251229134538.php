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
        $organizations = DB::table('organizations')->get();

        foreach ($organizations as $org) {
            if ($org->working_days && !str_starts_with($org->working_days, '[')) {
                $daysArray = array_map('trim', explode(',', $org->working_days));
                DB::table('organizations')
                    ->where('id', $org->id)
                    ->update([
                        'working_days' => json_encode($daysArray),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $organizations = DB::table('organizations')->get();

        foreach ($organizations as $org) {
            $days = json_decode($org->working_days, true);
            if (is_array($days)) {
                DB::table('organizations')
                    ->where('id', $org->id)
                    ->update([
                        'working_days' => implode(',', $days),
                    ]);
            }
        }
    }
};
