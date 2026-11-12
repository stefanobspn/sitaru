<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum StatusKepengurusanOrganisasi: string
{
    use HasValues;

    case TidakPernah = 'Tidak Pernah';
    case PernahMengurus = 'Pernah Menjadi Ketua Organisasi Sekolah';
}
