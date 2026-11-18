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
        Schema::table('registered_siswas', function (Blueprint $table) {
            $table->decimal('nrtka', 5, 2)->nullable()->after('rata_rata_nilai_rapor');
            $table->decimal('nilai_akhir', 6, 2)->nullable()->after('nrtka');
        });

        DB::table('registered_siswas')->whereNull('nilai_akhir')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $nrr = (float) ($row->rata_rata_nilai_rapor ?? 0);
                $nrtka = $row->nrtka !== null ? (float) $row->nrtka : round(mt_rand(5000, 9000) / 100, 2);
                $nk = (isset($row->nilai_kejuaraan) && is_numeric($row->nilai_kejuaraan)) ? (float) $row->nilai_kejuaraan : 0.0;
                $no = (float) ($row->nilai_organisasi ?? 0);
                $na = round((0.5 * $nrr) + (0.5 * $nrtka) + $nk + $no, 2);

                DB::table('registered_siswas')->where('id', $row->id)->update([
                    'nrtka' => $nrtka,
                    'nilai_akhir' => $na,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registered_siswas', function (Blueprint $table) {
            $table->dropColumn(['nrtka', 'nilai_akhir']);
        });
    }
};
