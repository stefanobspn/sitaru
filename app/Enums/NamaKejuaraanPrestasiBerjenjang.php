<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum NamaKejuaraanPrestasiBerjenjang: string
{
    use HasValues;

    case OSN = 'Olimpiade Sains Nasional (OSN)';
    case O2SN = 'Olimpiade Olahraga Siswa Nasional (O2SN)';
    case FLS2SN = 'Festival dan Lomba Seni Siswa Nasional (FLS2N)';
    case POPDA = 'Pekan Olahraga Pelajar Daerah (POPDA)';
    case MTQ = 'MTQ Pelajar';
    case LAINNYA = 'Lomba Berjenjang Resmi Lainnya';
}
