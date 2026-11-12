<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum StatusDomisili: string
{
    use HasValues;

    case SesuaiKK = 'Sesuai KK';
    case SesuaiMutasiOrtuWali = 'Sesuai Surat Mutasi Ortu/Wali';
    case SesuaiDomisiliPondok = 'Sesuai Domisili Pondok (EMIS)';
    case SesuaiDomisiliPantiAsuhan = 'Sesuai Domisili Panti Asuhan (Dinas Sosial)';
}
