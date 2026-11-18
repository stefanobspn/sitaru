<?php

namespace App\Models;

use Database\Factories\SekolahFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    /** @use HasFactory<SekolahFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'kuota' => 'integer',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function jurusans(): HasMany
    {
        return $this->hasMany(Jurusan::class);
    }

    public function isSmk(): bool
    {
        return $this->jenis_sekolah === 'SMK';
    }

    public function getNamaAttribute(): string
    {
        return $this->nama_sekolah;
    }
}
