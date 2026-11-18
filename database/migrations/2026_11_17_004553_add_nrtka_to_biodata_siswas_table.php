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
        Schema::table('biodata_siswas', function (Blueprint $table) {
            $table->decimal('nrtka', 5, 2)->nullable()->after('rw');
        });

        DB::table('biodata_siswas')->whereNull('nrtka')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('biodata_siswas')->where('id', $row->id)->update([
                    'nrtka' => round(mt_rand(5000, 9000) / 100, 2),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biodata_siswas', function (Blueprint $table) {
            $table->dropColumn('nrtka');
        });
    }
};
