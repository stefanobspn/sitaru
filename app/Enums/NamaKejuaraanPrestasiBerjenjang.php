<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum NamaKejuaraanPrestasiBerjenjang: string
{
    use HasValues;

    // 1. Kelompok Riset dan Inovasi
    case OSN = 'Olimpiade Sains Nasional (OSN)/Kompetisi Sains Nasional (KSN)';
    case OPSI = 'Olimpiade Penelitian Siswa Indonesia (OPSI)/Kompetisi Penelitian Siswa Indonesia (KOPSI)';
    case KIHAJAR = 'Kuis Kita Harus Belajar (Kihajar)';
    case OMI = 'Olimpiade Madrasah Indonesia (OMI)/Kompetisi Sains Madrasah (KSM)';
    case MYRES = 'Madrasah Young Researchers Supercamp (MYRES)';
    case OSMA = 'Olimpiade Sains Madrasah (OSMA)';

    // 2. Kelompok Olahraga
    case O2SN = 'Olimpiade Olahraga Siswa Nasional (O2SN)/Kompetisi Olahraga Siswa Nasional (KOSN)';
    case POSPENAS = 'Pekan Olahraga dan Seni Pondok Pesantren Nasional (Pospenas)';
    case GSI = 'Gala Siswa Nasional (GSI)';
    case POPDA_POPNAS = 'Pekan Olahraga Pelajar Daerah/Nasional (POPDA/POPNAS)';
    case PEPAPERDA = 'Pekan Paralympic Olahraga Pelajar Daerah/Nasional (Pepaperda/Pepapernas)';
    case PEPARNAS = 'Pekan Paralympic Olahraga Nasional (Peparnas)/Pekan Paralympic Olahraga Provinsi (Peparprov)/Pekan Paralympic Olahraga Kabupaten/Kota (Peparkab/Peparkot)';
    case PON = 'Pekan Olahraga Nasional (PON)/Pekan Olahraga Provinsi (PORPROV)/Pekan Olahraga Kabupaten/Kota (PORKAB/PORKOT)';
    case PORSADIN = 'Porsadin (Pekan Olahraga dan Seni Antar Diniyah)';

    // 3. Kelompok Seni dan Budaya
    case FLS2N = 'Festival dan Lomba Seni Siswa Nasional (FLS2N)';
    case OLSN = 'Olimpiade Literasi Siswa Nasional (OLSN)';
    case LCSPN = 'Lomba Cipta Seni Pelajar Nasional (LCSPN)';
    case LKSN = 'Lomba Keterampilan Siswa Nasional (LKSN)';
    case FTBI = 'Festival Tunas Bahasa Ibu (FTBI)';
    case PORSENI_MTS = 'Pekan Olahraga dan Seni (Porseni) MTs';
    case LCCM = 'Lomba Cerdas Cermat Museum (LCCM)';

    // 4. Kelompok Keagamaan
    case MTQ = 'Musabaqah Tilawatil Qur\'an (MTQ) Pelajar';
    case MQK = 'Musabaqoh Qiroatul Kutub (MQK)';
    case MAPSI = 'Lomba Mata Pelajaran Pendidikan Agama Islam dan Seni Islami (MAPSI)';
    case SDS = 'Sippa Dhamma Samajja (SDS)';
    case UDG = 'Utsawa Dharma Gita (UDG)';
    case PESPARANI = 'Pesta Paduan Suara Gerejani (Pesparani)';

    // 5. Kelompok Kepanduan dan lainnya
    case LT_PRAMUKA = 'Lomba Tingkat (LT) Pramuka Penggalang';
    case PRAMUKA_GARUDA = 'Lomba Pramuka Garuda Berprestasi/Pramuka Teladan';
    case FASI = 'Festival Anak Sholeh Indonesia (FASI)';
}
