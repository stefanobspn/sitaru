<?php

namespace App\Models;

use App\Enums\StatusKhusus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisteredSiswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'nilai_rapor' => 'array',
            'is_activated' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'rata_rata_nilai_rapor' => 'float',
            'nrtka' => 'float',
            'nilai_akhir' => 'float',
            'tgl_cetak_kk' => 'date',
            'tgl_surat_penugasan' => 'date',
            'is_anak_guru' => 'boolean',
            'tgl_pilih_sekolah' => 'datetime',
            'is_daftar_ulang' => 'boolean',
        ];
    }

    public function hitungNilaiAkhir(): float
    {
        $nrr = (float) ($this->rata_rata_nilai_rapor ?? 0);
        $nrtka = (float) ($this->nrtka ?? 0);
        $nk = is_numeric($this->nilai_kejuaraan) ? (float) $this->nilai_kejuaraan : 0.0;
        $no = (float) ($this->nilai_organisasi ?? 0);

        return round((0.5 * $nrr) + (0.5 * $nrtka) + $nk + $no, 2);
    }

    public function biodataSiswa(): BelongsTo
    {
        return $this->belongsTo(BiodataSiswa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pilihanSekolah1(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'pilihan_sekolah_1_id');
    }

    public function pilihanSekolah2(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'pilihan_sekolah_2_id');
    }

    public function diterimaSekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'diterima_sekolah_id');
    }

    public function pilihanJurusan1(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'pilihan_jurusan_1_id');
    }

    public function pilihanJurusan2(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'pilihan_jurusan_2_id');
    }

    public function diterimaJurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'diterima_jurusan_id');
    }
}
