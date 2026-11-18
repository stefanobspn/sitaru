<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum NamaKejuaraanInternasionalBerjenjang: string
{
    use HasValues;

    case IMSO = 'International Mathematics and Science Olympiad (IMSO)';
    case ITMO = 'International Teenagers Mathematics Olympiad (ITMO)';
    case IPHO = 'International Physics Olympiad (IPhO)';
    case ICHO = 'International Chemistry Olympiad (IChO)';
    case IBO = 'International Biology Olympiad (IBO)';
    case IGEO = 'International Geography Olympiad (IGeO)';
    case IOAA = 'International Olympiad on Astronomy and Astrophysics (IOAA)';
    case IOI = 'International Olympiad in Informatics (IOI)';
    case APIO = 'The Asia Pasific Informatic Olympiad (APIO)';
    case ASEAN_SCHOOL_GAMES = 'Asean School Games';
    case SEA_GAMES = 'SEA Games';
    case ASEAN_PARAGAMES = 'Asean Paragames';
    case ASIAN_PARAGAMES = 'Asian Paragames';
    case OLYMPIADE = 'Olympiade';
    case MTQ_INTERNASIONAL = 'MTQ Internasional';
}
