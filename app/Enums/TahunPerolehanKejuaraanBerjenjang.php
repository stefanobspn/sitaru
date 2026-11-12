<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum TahunPerolehanKejuaraanBerjenjang: string
{
    use HasValues;

    case Tahun2025 = 'Tahun 2025';
    case Tahun2024 = 'Tahun 2024';
}
