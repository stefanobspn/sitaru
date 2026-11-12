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
        Schema::create('registered_siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran')->unique();
            $table->foreignId('biodata_siswa_id')->nullable()->constrained('biodata_siswas');

            // biodata
            $table->string('nisn', 10);
            $table->string('nik', 16);
            $table->string('nama_lengkap');
            $table->string('nama_sekolah_asal');
            $table->string('tahun_lulus');
            $table->string('wilayah_sekolah_asal');

            // kontak & domisili
            $table->string('no_whatsapp');
            $table->string('email');
            $table->string('status_domisili');
            $table->date('tgl_cetak_kk')->nullable();
            $table->string('pasted_coordinate');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // akademik & prestasi
            $table->json('nilai_rapor');
            $table->decimal('rata_rata_nilai_rapor', 5, 2);
            $table->string('status_khusus');
            $table->string('status_prestasi');
            $table->string('kategori_event_prestasi')->nullable();
            $table->string('tingkat_kejuaraan_berjenjang')->nullable();
            $table->string('nama_kejuaraan_berjenjang')->nullable();
            $table->string('peringkat_juara_berjenjang')->nullable();
            $table->string('tahun_perolehan_kejuaraan_berjenjang')->nullable();
            $table->string('penyelenggara_kejuaraan_berjenjang')->nullable();
            $table->string('status_kepengurusan_organisasi');
            $table->string('jenis_organisasi_jabatan')->nullable();

            // berkas
            $table->string('surat_pernyataan_kebenaran_path');
            $table->string('kartu_keluarga_path');
            $table->string('surat_keterangan_nilai_rapor_path');
            $table->string('piagam_prestasi_path')->nullable();
            $table->string('sk_organisasi_path')->nullable();

            // status aktivasi
            $table->boolean('is_activated')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered_siswas');
    }
};
