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
            $table->string('tingkat_kejuaraan_tidak_berjenjang')->nullable()->after('penyelenggara_kejuaraan_berjenjang');
            $table->string('peringkat_kurasi_tidak_berjenjang')->nullable()->after('tingkat_kejuaraan_tidak_berjenjang');
            $table->string('nama_kejuaraan_tidak_berjenjang')->nullable()->after('peringkat_kurasi_tidak_berjenjang');
            $table->string('peringkat_juara_tidak_berjenjang')->nullable()->after('nama_kejuaraan_tidak_berjenjang');
            $table->string('tahun_perolehan_kejuaraan_tidak_berjenjang')->nullable()->after('peringkat_juara_tidak_berjenjang');
            $table->string('penyelenggara_kejuaraan_tidak_berjenjang')->nullable()->after('tahun_perolehan_kejuaraan_tidak_berjenjang');
            $table->string('nilai_kejuaraan')->nullable()->after('jenis_organisasi_jabatan');
            $table->decimal('nilai_organisasi', 4, 2)->default(0)->after('nilai_kejuaraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registered_siswas', function (Blueprint $table) {
            $table->dropColumn([
                'tingkat_kejuaraan_tidak_berjenjang',
                'peringkat_kurasi_tidak_berjenjang',
                'nama_kejuaraan_tidak_berjenjang',
                'peringkat_juara_tidak_berjenjang',
                'tahun_perolehan_kejuaraan_tidak_berjenjang',
                'penyelenggara_kejuaraan_tidak_berjenjang',
                'nilai_kejuaraan',
                'nilai_organisasi',
            ]);
        });
    }
};
