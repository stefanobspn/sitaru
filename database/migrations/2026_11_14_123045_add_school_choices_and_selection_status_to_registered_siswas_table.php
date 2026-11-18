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
            $table->foreignId('pilihan_sekolah_1_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->foreignId('pilihan_sekolah_2_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->string('status_seleksi')->default('belum_memilih');
            $table->foreignId('diterima_sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->boolean('is_daftar_ulang')->default(false);
            $table->timestamp('tgl_pilih_sekolah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registered_siswas', function (Blueprint $table) {
            $table->dropForeign(['pilihan_sekolah_1_id']);
            $table->dropForeign(['pilihan_sekolah_2_id']);
            $table->dropForeign(['diterima_sekolah_id']);
            $table->dropColumn([
                'pilihan_sekolah_1_id',
                'pilihan_sekolah_2_id',
                'status_seleksi',
                'diterima_sekolah_id',
                'is_daftar_ulang',
                'tgl_pilih_sekolah',
            ]);
        });
    }
};
