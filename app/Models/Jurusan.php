<?php

namespace App\Models;

use Database\Factories\JurusanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jurusan extends Model
{
    /** @use HasFactory<JurusanFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
}
