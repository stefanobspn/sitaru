<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum WilayahSekolahAsal: string
{
    use HasValues;

    case DalamProvinsi = 'Dalam Provinsi';
    case LuarProvinsi = 'Luar Provinsi';
}
