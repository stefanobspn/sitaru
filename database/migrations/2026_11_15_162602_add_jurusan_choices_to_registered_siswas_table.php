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
            $table->foreignId('pilihan_jurusan_1_id')->nullable()->after('pilihan_sekolah_1_id')->constrained('jurusans')->nullOnDelete();
            $table->foreignId('pilihan_jurusan_2_id')->nullable()->after('pilihan_sekolah_2_id')->constrained('jurusans')->nullOnDelete();
            $table->foreignId('diterima_jurusan_id')->nullable()->after('diterima_sekolah_id')->constrained('jurusans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registered_siswas', function (Blueprint $table) {
            $table->dropForeign(['pilihan_jurusan_1_id']);
            $table->dropForeign(['pilihan_jurusan_2_id']);
            $table->dropForeign(['diterima_jurusan_id']);
            $table->dropColumn(['pilihan_jurusan_1_id', 'pilihan_jurusan_2_id', 'diterima_jurusan_id']);
        });
    }
};
