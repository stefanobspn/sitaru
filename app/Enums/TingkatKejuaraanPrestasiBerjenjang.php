<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum TingkatKejuaraanPrestasiBerjenjang: string
{
    use HasValues;

    case Internasional = 'Tingkat Internasional';
    case Nasional = 'Tingkat Nasional';
    case Provinsi = 'Tingkat Provinsi';
    case KabKota = 'Tingkat Kab/Kota';
}
