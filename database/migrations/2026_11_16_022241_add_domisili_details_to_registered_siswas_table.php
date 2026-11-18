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
            // mutasi ortu/wali
            $table->string('nama_instansi_mutasi')->nullable()->after('tgl_cetak_kk');
            $table->string('nomor_surat_penugasan')->nullable()->after('nama_instansi_mutasi');
            $table->date('tgl_surat_penugasan')->nullable()->after('nomor_surat_penugasan');
            $table->string('nomor_sk_domisili')->nullable()->after('tgl_surat_penugasan');
            $table->boolean('is_anak_guru')->default(false)->after('nomor_sk_domisili');

            // pondok pesantren (EMIS)
            $table->string('nama_pondok_pesantren')->nullable()->after('is_anak_guru');
            $table->string('nomor_izin_pondok_emis')->nullable()->after('nama_pondok_pesantren');
            $table->string('jenjang_pondok')->nullable()->after('nomor_izin_pondok_emis');

            // panti asuhan (Dinas Sosial)
            $table->string('nama_panti_asuhan')->nullable()->after('jenjang_pondok');
            $table->string('nomor_registrasi_dinsos')->nullable()->after('nama_panti_asuhan');
            $table->string('prioritas_panti')->nullable()->after('nomor_registrasi_dinsos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registered_siswas', function (Blueprint $table) {
            $table->dropColumn([
                'nama_instansi_mutasi',
                'nomor_surat_penugasan',
                'tgl_surat_penugasan',
                'nomor_sk_domisili',
                'is_anak_guru',
                'nama_pondok_pesantren',
                'nomor_izin_pondok_emis',
                'jenjang_pondok',
                'nama_panti_asuhan',
                'nomor_registrasi_dinsos',
                'prioritas_panti',
            ]);
        });
    }
};
