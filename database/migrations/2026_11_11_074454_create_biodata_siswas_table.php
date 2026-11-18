<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biodata_siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nisn');
            $table->string('nik');
            $table->string('nama_lengkap');
            $table->string('nama_sekolah_asal');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('alamat');
            $table->string('provinsi');
            $table->string('kabupaten_kota');
            $table->string('kecamatan');
            $table->string('kelurahan');
            $table->string('rt');
            $table->string('rw');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biodata_siswas');
    }
};
