<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSiswaSeeder extends Seeder
{
    /**
     * Seed dummy registered students for demonstration and E2E automated tests.
     */
    public function run(): void
    {
        $sekolah1 = Sekolah::where('npsn', '20327981')->first();
        $sekolah2 = Sekolah::where('npsn', '20327982')->first();
        $sekolah3 = Sekolah::where('npsn', '20327983')->first();
        $sekolah4 = Sekolah::where('npsn', '20327984')->first();

        if (! $sekolah1 || ! $sekolah2) {
            return;
        }

        $jurusanRpl = $sekolah4?->jurusans()->where('nama_jurusan', 'Rekayasa Perangkat Lunak')->first();
        $jurusanTkj = $sekolah4?->jurusans()->where('nama_jurusan', 'Teknik Komputer dan Jaringan')->first();

        // 1. Siswa Utama untuk Demo / Testing Login (Budi Pratama)
        $demoUserSiswa = User::query()->updateOrCreate(
            ['nisn' => '1234567890'],
            [
                'name' => 'Budi Pratama',
                'email' => 'budi@sitaru.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Siswa,
            ]
        );

        $existingDemo = RegisteredSiswa::where('nisn', '1234567890')->first();
        $demoData = [
            'user_id' => $demoUserSiswa->id,
            'nomor_pendaftaran' => 'REG-2026-000001',
            'nama_lengkap' => 'Budi Pratama',
            'nik' => '3372010101080001',
            'nama_sekolah_asal' => 'SMP Negeri 1 Surakarta',
            'tahun_lulus' => '2026',
            'no_whatsapp' => '081234567890',
            'status_domisili' => 'Dalam Daerah',
            'rata_rata_nilai_rapor' => 88.50,
            'nrtka' => 85.00,
            'nilai_akhir' => 86.75,
            'status_khusus' => 'Tidak Ada',
            'status_prestasi' => 'Juara 1 OSN Matematika Kota',
            'is_activated' => true,
            'status_verifikasi' => 'terverifikasi',
            'activation_code' => 'ACT-BUDIPRT1',
            'operator_nip' => $sekolah1->operator?->nip ?? '198501012010011001',
            'pilihan_sekolah_1_id' => $sekolah1->id,
            'pilihan_sekolah_2_id' => $sekolah2->id,
            'status_seleksi' => 'menunggu',
            'is_daftar_ulang' => false,
            'tgl_pilih_sekolah' => now()->subHours(5),
        ];

        if ($existingDemo) {
            $existingDemo->update($demoData);
        } else {
            RegisteredSiswa::factory()->create(array_merge($demoData, ['nisn' => '1234567890']));
        }

        // 2. Data Siswa Tambahan dengan Variasi Nilai untuk Menguji Perangkingan & Kuota
        $dummyStudents = [
            ['nama' => 'Aditya Wijaya', 'nisn' => '0081234501', 'nilai' => 95.80, 's1' => $sekolah1->id, 's2' => $sekolah2->id],
            ['nama' => 'Citra Lestari', 'nisn' => '0081234502', 'nilai' => 94.25, 's1' => $sekolah1->id, 's2' => $sekolah3?->id ?? $sekolah2->id],
            ['nama' => 'Dimas Anggara', 'nisn' => '0081234503', 'nilai' => 92.50, 's1' => $sekolah2->id, 's2' => $sekolah1->id],
            ['nama' => 'Eka Putri Handayani', 'nisn' => '0081234504', 'nilai' => 91.00, 's1' => $sekolah1->id, 's2' => $sekolah2->id],
            ['nama' => 'Fajar Nugroho', 'nisn' => '0081234505', 'nilai' => 89.75, 's1' => $sekolah2->id, 's2' => $sekolah3?->id ?? $sekolah1->id],
            ['nama' => 'Gita Maharani', 'nisn' => '0081234506', 'nilai' => 87.20, 's1' => $sekolah3?->id ?? $sekolah1->id, 's2' => $sekolah1->id],
            ['nama' => 'Hendra Saputra', 'nisn' => '0081234507', 'nilai' => 86.40, 's1' => $sekolah1->id, 's2' => $sekolah3?->id ?? $sekolah2->id],
            ['nama' => 'Intan Permatasari', 'nisn' => '0081234508', 'nilai' => 85.90, 's1' => $sekolah2->id, 's2' => $sekolah1->id],
            ['nama' => 'Joko Susilo', 'nisn' => '0081234509', 'nilai' => 84.10, 's1' => $sekolah3?->id ?? $sekolah1->id, 's2' => $sekolah2->id],
            ['nama' => 'Kartika Sari', 'nisn' => '0081234510', 'nilai' => 83.50, 's1' => $sekolah1->id, 's2' => $sekolah2->id],
            ['nama' => 'Lukman Hakim', 'nisn' => '0081234511', 'nilai' => 82.00, 's1' => $sekolah2->id, 's2' => $sekolah3?->id ?? $sekolah1->id],
            ['nama' => 'Mega Utami', 'nisn' => '0081234512', 'nilai' => 80.75, 's1' => $sekolah3?->id ?? $sekolah1->id, 's2' => $sekolah1->id],
            ['nama' => 'Naufal Rizky', 'nisn' => '0081234513', 'nilai' => 79.30, 's1' => $sekolah1->id, 's2' => $sekolah3?->id ?? $sekolah2->id],
            ['nama' => 'Olivia Zahra', 'nisn' => '0081234514', 'nilai' => 78.60, 's1' => $sekolah2->id, 's2' => $sekolah1->id],
            ['nama' => 'Pandu Wicaksono', 'nisn' => '0081234515', 'nilai' => 88.50, 's1' => $sekolah4?->id ?? $sekolah1->id, 's2' => null, 'j1' => $jurusanRpl?->id, 'j2' => null],
            ['nama' => 'Qori Amanda', 'nisn' => '0081234516', 'nilai' => 86.00, 's1' => $sekolah4?->id ?? $sekolah1->id, 's2' => null, 'j1' => $jurusanTkj?->id, 'j2' => null],
        ];

        foreach ($dummyStudents as $idx => $dummy) {
            $regNum = 'REG-2026-'.str_pad((string) ($idx + 2), 6, '0', STR_PAD_LEFT);
            $actCode = 'ACT-'.strtoupper(Str::random(8));

            $user = User::query()->updateOrCreate(
                ['nisn' => $dummy['nisn']],
                [
                    'name' => $dummy['nama'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Siswa,
                ]
            );

            $dummyNrtka = round(mt_rand(7000, 9000) / 100, 2);
            $dummyNilaiAkhir = round((0.5 * $dummy['nilai']) + (0.5 * $dummyNrtka), 2);

            $siswaAttributes = [
                'user_id' => $user->id,
                'nomor_pendaftaran' => $regNum,
                'nama_lengkap' => $dummy['nama'],
                'nik' => '33720101010800'.str_pad((string) ($idx + 2), 2, '0', STR_PAD_LEFT),
                'nama_sekolah_asal' => 'SMP Negeri '.(($idx % 4) + 1).' Surakarta',
                'tahun_lulus' => '2026',
                'no_whatsapp' => '0812345678'.str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                'status_domisili' => 'Dalam Daerah',
                'rata_rata_nilai_rapor' => $dummy['nilai'],
                'nrtka' => $dummyNrtka,
                'nilai_akhir' => $dummyNilaiAkhir,
                'status_khusus' => 'Tidak Ada',
                'status_prestasi' => $idx % 2 === 0 ? 'Prestasi Akademik' : 'Tidak Ada',
                'is_activated' => true,
                'status_verifikasi' => 'terverifikasi',
                'activation_code' => $actCode,
                'operator_nip' => $sekolah1->operator?->nip ?? '198501012010011001',
                'pilihan_sekolah_1_id' => $dummy['s1'],
                'pilihan_sekolah_2_id' => $dummy['s2'],
                'pilihan_jurusan_1_id' => $dummy['j1'] ?? null,
                'pilihan_jurusan_2_id' => $dummy['j2'] ?? null,
                'status_seleksi' => 'menunggu',
                'is_daftar_ulang' => false,
                'tgl_pilih_sekolah' => now()->subHours(10 - ($idx % 5)),
            ];

            $existing = RegisteredSiswa::where('nisn', $dummy['nisn'])->first();
            if ($existing) {
                $existing->update($siswaAttributes);
            } else {
                RegisteredSiswa::factory()->create(array_merge($siswaAttributes, ['nisn' => $dummy['nisn']]));
            }
        }

        // 3. Data Siswa Terverifikasi yang Belum Diaktivasi (Dewi Sartika)
        $existingDewi = RegisteredSiswa::where('nisn', '0077889900')->first();
        $dewiData = [
            'nomor_pendaftaran' => 'REG-2026-999999',
            'nama_lengkap' => 'Dewi Sartika',
            'nik' => '3372010101097788',
            'nama_sekolah_asal' => 'SMP Negeri 2 Surakarta',
            'tahun_lulus' => '2026',
            'wilayah_sekolah_asal' => 'Dalam Daerah',
            'no_whatsapp' => '081299887766',
            'status_domisili' => 'Dalam Daerah',
            'rata_rata_nilai_rapor' => 89.00,
            'nrtka' => 80.00,
            'nilai_akhir' => 84.50,
            'status_khusus' => 'Tidak Ada',
            'status_prestasi' => 'Tidak Ada',
            'is_activated' => false,
            'status_verifikasi' => 'terverifikasi',
            'activation_code' => 'ACT-DEWI2026',
            'operator_nip' => $sekolah1->operator?->nip ?? '198501012010011001',
            'user_id' => null,
        ];
        if ($existingDewi) {
            $existingDewi->update($dewiData);
        } else {
            RegisteredSiswa::factory()->create(array_merge($dewiData, ['nisn' => '0077889900']));
        }
    }
}
