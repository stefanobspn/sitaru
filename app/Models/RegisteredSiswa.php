<?php

namespace App\Models;

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
            'tgl_cetak_kk' => 'date'
        ];
    }

    public function biodataSiswa(): BelongsTo
    {
        return $this->belongsTo(BiodataSiswa::class);
    }
}
