<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum KategoriEventPrestasi: string
{
    use HasValues;

    case Berjenjang = 'Berjenjang';
    case TidakBerjenjang = 'Tidak Berjenjang';
}
