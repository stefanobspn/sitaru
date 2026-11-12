<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PeringkatJuaraBerjenjang: string
{
    use HasValues;

    case JuaraSatu = 'Juara 1';
    case JuaraDua = 'Juara 2';
    case JuaraTiga = 'Juara 3';
}
