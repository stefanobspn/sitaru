<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PeringkatKurasiTidakBerjenjang: string
{
    use HasValues;

    case Bintang5 = 'Kurasi: Bintang 5';
    case Bintang4 = 'Kurasi: Bintang 4';
    case Bintang3 = 'Kurasi: Bintang 3';
    case Bintang2 = 'Kurasi: Bintang 2';
    case Bintang1 = 'Kurasi: Bintang 1';
    case NonKurasi = 'Non Kurasi';
}
