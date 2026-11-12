<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum JenisOrganisasiJabatan: string
{
    use HasValues;

    case OSIS = 'Ketua OSIS (Organisasi Siswa Intra Sekolah';
    case OSIM = 'Ketua OSIM (Organisasi Siswa Intra Madrasah';
    case MPK = 'Ketua MPK (Majelis Perwakilan Kelas';
    case BES = 'Ketua Badan Eksekutif Siswa (BES)';
    case PRAMUKA = 'Ketua Pramuka (Pratama Putra / Putri)';
    case HIZBUL_WATHAN = 'Ketua Hizbul Wathan (Pratama Putra / Putri';
}
