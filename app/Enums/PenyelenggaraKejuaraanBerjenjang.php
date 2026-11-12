<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PenyelenggaraKejuaraanBerjenjang: string
{
    use HasValues;

    case BPTI = 'BPTI';
    case NotBPTI = 'Selain BPTI';
}
