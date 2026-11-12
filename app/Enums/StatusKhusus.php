<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum StatusKhusus: string
{
    use HasValues;

    case TidakAda = 'Tidak Ada Status Khusus';
    case AnakPantiAsuhan = 'Anak Panti Asuhan';
    case KeluargaEkonomiTidakMampu = 'Keluarga Ekonomi Tidak Mampu';
    case AnakGuru = 'Anak Guru';
    case PenyandangDisabilitas = 'Penyandang Disabilitas';
    case AnakTidakSekolah = 'Anak Tidak Sekolah';
}
