<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BiodataSiswa;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Super Administrator
        User::query()->updateOrCreate(
            ['email' => 'admin@sitaru.test'],
            [
                'name' => 'Administrator SITARU',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        // 2. Daftar Sekolah dan Akun Operator masing-masing
        $sekolahData = [
            [
                'nama_sekolah' => 'SMA Negeri 1 Surakarta',
                'npsn' => '20327981',
                'jenis_sekolah' => 'SMA',
                'kuota' => 5,
                'nip_operator' => '198501012010011001',
                'nama_operator' => 'Operator SMAN 1 Surakarta',
            ],
            [
                'nama_sekolah' => 'SMA Negeri 2 Surakarta',
                'npsn' => '20327982',
                'jenis_sekolah' => 'SMA',
                'kuota' => 5,
                'nip_operator' => '198602022010011002',
                'nama_operator' => 'Operator SMAN 2 Surakarta',
            ],
            [
                'nama_sekolah' => 'SMA Negeri 3 Surakarta',
                'npsn' => '20327983',
                'jenis_sekolah' => 'SMA',
                'kuota' => 4,
                'nip_operator' => '198703032010011003',
                'nama_operator' => 'Operator SMAN 3 Surakarta',
            ],
            [
                'nama_sekolah' => 'SMK Negeri 2 Surakarta',
                'npsn' => '20327984',
                'jenis_sekolah' => 'SMK',
                'kuota' => 6,
                'nip_operator' => '198804042010011004',
                'nama_operator' => 'Operator SMKN 2 Surakarta',
                'jurusans' => [
                    'Rekayasa Perangkat Lunak',
                    'Teknik Komputer dan Jaringan',
                    'Teknik Kendaraan Ringan Otomotif',
                ],
            ],
        ];
        foreach ($sekolahData as $data) {
            $operator = User::query()->updateOrCreate(
                ['nip' => $data['nip_operator']],
                [
                    'name' => $data['nama_operator'],
                    'email' => "operator_{$data['npsn']}@sitaru.test",
                    'password' => Hash::make('password'),
                    'role' => UserRole::Operator,
                ]
            );

            $sekolah = Sekolah::query()->updateOrCreate(
                ['npsn' => $data['npsn']],
                [
                    'nama_sekolah' => $data['nama_sekolah'],
                    'jenis_sekolah' => $data['jenis_sekolah'] ?? 'SMA',
                    'operator_id' => $operator->id,
                    'kuota' => $data['kuota'],
                ]
            );

            if (! empty($data['jurusans'])) {
                foreach ($data['jurusans'] as $namaJurusan) {
                    $sekolah->jurusans()->firstOrCreate(
                        ['nama_jurusan' => $namaJurusan]
                    );
                }
            }

        }

        // 3. Data Calon Siswa di BiodataSiswa (CSV Asli & Pengujian Wizard)
        $this->call(BiodataSiswaCsvSeeder::class);

        BiodataSiswa::query()->updateOrCreate(
            ['nisn' => '1234567890'],
            [
                'nik' => '3372010101080001',
                'nama_lengkap' => 'Budi Pratama',
                'nama_sekolah_asal' => 'SMP Negeri 1 Surakarta',
                'tempat_lahir' => 'Surakarta',
                'tanggal_lahir' => '2008-05-12',
                'alamat' => 'Jl. Slamet Riyadi No. 12',
                'provinsi' => 'Jawa Tengah',
                'kabupaten_kota' => 'Kota Surakarta',
                'kecamatan' => 'Laweyan',
                'kelurahan' => 'Kerten',
                'rt' => '001',
                'rw' => '002',
                'nrtka' => 85.00,
            ]
        );

        BiodataSiswa::query()->updateOrCreate(
            ['nisn' => '0099887766'],
            [
                'nik' => '3372010101090099',
                'nama_lengkap' => 'Rian Hidayat',
                'nama_sekolah_asal' => 'SMP Negeri 1 Surakarta',
                'tempat_lahir' => 'Surakarta',
                'tanggal_lahir' => '2010-05-15',
                'alamat' => 'Jl. Slamet Riyadi No. 100',
                'provinsi' => 'Jawa Tengah',
                'kabupaten_kota' => 'Surakarta',
                'kecamatan' => 'Banjarsari',
                'kelurahan' => 'Kestalan',
                'rt' => '001',
                'rw' => '002',
                'nrtka' => 78.50,
            ]
        );
    }
}
