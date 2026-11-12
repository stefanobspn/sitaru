<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum StatusPrestasi: string
{
    use HasValues;

    case TidakMemiliki = 'Tidak Memiliki Kejuaraan / Prestasi';
    case Memiliki = 'Memiliki Kejuaraan / Prestasi';
}
