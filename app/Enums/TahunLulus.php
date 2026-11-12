<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum TahunLulus: string
{
    use HasValues;

    case Tahun2026 = '2026';
    case Sebelum2026 = 'Sebelum 2026';
}
