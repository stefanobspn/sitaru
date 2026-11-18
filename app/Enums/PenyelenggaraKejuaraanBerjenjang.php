<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PenyelenggaraKejuaraanBerjenjang: string
{
    use HasValues;

    case PemerintahDaerah = 'Pemerintah Daerah';
    case KementerianLembaga = 'Kementerian / Lembaga Pemerintah Non-Kementerian';
}
