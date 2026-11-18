<?php

namespace App\Services;

use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SeleksiService
{
    /**
     * Menghitung alokasi penempatan merit-based untuk seluruh pendaftar.
     * Mekanisme:
     * 1. Siswa diurutkan berdasarkan Nilai Akhir (NA) DESC, rata-rata nilai rapor DESC, created_at ASC.
     * 2. Siswa ditempatkan di Pilihan 1 jika kuota Pilihan 1 masih tersedia.
     * 3. Jika Pilihan 1 penuh (tergeser), siswa otomatis ditempatkan di Pilihan 2 jika kuota Pilihan 2 masih tersedia.
     * 4. Jika Pilihan 2 juga penuh (atau tidak memilih Pilihan 2), siswa tergeser di kedua pilihan.
     *
     * @return array{
     *     admitted_by_school: array<int, Collection>,
     *     status_by_siswa: array<int, array{
     *         status: string,
     *         diterima_sekolah_id: int|null,
     *         diterima_sekolah: Sekolah|null,
     *         penempatan_pilihan: int|null,
     *         rank: int|null,
     *         is_tergeser_pilihan_1: bool,
     *         is_tergeser_pilihan_2: bool,
     *         is_tergeser_semua: bool
     *     }>,
     *     total_pendaftar_by_school: array<int, int>
     * }
     */
    public static function hitungPenempatan(): array
    {
        $sekolahs = Sekolah::all()->keyBy('id');
        $kuotaTersisa = $sekolahs->mapWithKeys(fn ($s) => [$s->id => (int) $s->kuota])->all();

        $admittedBySchool = [];
        $totalPendaftarBySchool = [];
        foreach ($sekolahs as $sekolahId => $s) {
            $admittedBySchool[$sekolahId] = collect();
            $totalPendaftarBySchool[$sekolahId] = 0;
        }

        // Ambil semua pendaftar yang sudah memilih sekolah
        $siswas = RegisteredSiswa::query()
            ->with(['pilihanJurusan1', 'pilihanJurusan2'])
            ->whereNotNull('pilihan_sekolah_1_id')
            ->orderByRaw('COALESCE(nilai_akhir, rata_rata_nilai_rapor) DESC')
            ->orderByDesc('rata_rata_nilai_rapor')
            ->orderBy('created_at')
            ->get();

        // Hitung total pendaftar yang memilih masing-masing sekolah (pilihan 1 atau 2)
        foreach ($siswas as $siswa) {
            if ($siswa->pilihan_sekolah_1_id && isset($totalPendaftarBySchool[$siswa->pilihan_sekolah_1_id])) {
                $totalPendaftarBySchool[$siswa->pilihan_sekolah_1_id]++;
            }
            if ($siswa->pilihan_sekolah_2_id && isset($totalPendaftarBySchool[$siswa->pilihan_sekolah_2_id])) {
                $totalPendaftarBySchool[$siswa->pilihan_sekolah_2_id]++;
            }
        }

        $statusBySiswa = [];

        foreach ($siswas as $siswa) {
            $pilihan1Id = $siswa->pilihan_sekolah_1_id;
            $pilihan2Id = $siswa->pilihan_sekolah_2_id;

            // 1. Cek kuota Pilihan 1
            if ($pilihan1Id && isset($kuotaTersisa[$pilihan1Id]) && $kuotaTersisa[$pilihan1Id] > 0) {
                $kuotaTersisa[$pilihan1Id]--;
                $rank = $admittedBySchool[$pilihan1Id]->count() + 1;

                $siswaClone = clone $siswa;
                $siswaClone->rank = $rank;
                $siswaClone->penempatan_pilihan = 1;
                $siswaClone->jurusan_terpilih = $siswa->pilihanJurusan1;
                $admittedBySchool[$pilihan1Id]->push($siswaClone);

                $statusBySiswa[$siswa->id] = [
                    'status' => 'lolos_pilihan_1',
                    'diterima_sekolah_id' => $pilihan1Id,
                    'diterima_sekolah' => $sekolahs[$pilihan1Id] ?? null,
                    'diterima_jurusan_id' => $siswa->pilihan_jurusan_1_id,
                    'diterima_jurusan' => $siswa->pilihanJurusan1,
                    'penempatan_pilihan' => 1,
                    'rank' => $rank,
                    'is_tergeser_pilihan_1' => false,
                    'is_tergeser_pilihan_2' => false,
                    'is_tergeser_semua' => false,
                ];

                continue;
            }

            // 2. Tergeser di Pilihan 1 -> Otomatis ditempatkan di Pilihan 2 jika masih ada kuota
            if ($pilihan2Id && isset($kuotaTersisa[$pilihan2Id]) && $kuotaTersisa[$pilihan2Id] > 0) {
                $kuotaTersisa[$pilihan2Id]--;
                $rank = $admittedBySchool[$pilihan2Id]->count() + 1;

                $siswaClone = clone $siswa;
                $siswaClone->rank = $rank;
                $siswaClone->penempatan_pilihan = 2;
                $siswaClone->jurusan_terpilih = $siswa->pilihanJurusan2;
                $admittedBySchool[$pilihan2Id]->push($siswaClone);

                $statusBySiswa[$siswa->id] = [
                    'status' => 'lolos_pilihan_2',
                    'diterima_sekolah_id' => $pilihan2Id,
                    'diterima_sekolah' => $sekolahs[$pilihan2Id] ?? null,
                    'diterima_jurusan_id' => $siswa->pilihan_jurusan_2_id,
                    'diterima_jurusan' => $siswa->pilihanJurusan2,
                    'penempatan_pilihan' => 2,
                    'rank' => $rank,
                    'is_tergeser_pilihan_1' => true,
                    'is_tergeser_pilihan_2' => false,
                    'is_tergeser_semua' => false,
                ];

                continue;
            }

            // 3. Tergeser di kedua pilihan
            $statusBySiswa[$siswa->id] = [
                'status' => 'tergeser_semua',
                'diterima_sekolah_id' => null,
                'diterima_sekolah' => null,
                'diterima_jurusan_id' => null,
                'diterima_jurusan' => null,
                'penempatan_pilihan' => null,
                'rank' => null,
                'is_tergeser_pilihan_1' => true,
                'is_tergeser_pilihan_2' => (bool) $pilihan2Id,
                'is_tergeser_semua' => true,
            ];
        }

        return [
            'admitted_by_school' => $admittedBySchool,
            'status_by_siswa' => $statusBySiswa,
            'total_pendaftar_by_school' => $totalPendaftarBySchool,
        ];
    }

    /**
     * Finalisasi hasil seleksi ke basis data oleh Admin.
     */
    public static function finalisasi(): void
    {
        DB::transaction(function () {
            $hasil = self::hitungPenempatan();
            $statusBySiswa = $hasil['status_by_siswa'];

            foreach ($statusBySiswa as $siswaId => $data) {
                if ($data['status'] === 'lolos_pilihan_1') {
                    RegisteredSiswa::where('id', $siswaId)->update([
                        'status_seleksi' => 'diterima_pilihan_1',
                        'diterima_sekolah_id' => $data['diterima_sekolah_id'],
                        'diterima_jurusan_id' => $data['diterima_jurusan_id'] ?? null,
                    ]);
                } elseif ($data['status'] === 'lolos_pilihan_2') {
                    RegisteredSiswa::where('id', $siswaId)->update([
                        'status_seleksi' => 'diterima_pilihan_2',
                        'diterima_sekolah_id' => $data['diterima_sekolah_id'],
                        'diterima_jurusan_id' => $data['diterima_jurusan_id'] ?? null,
                    ]);
                } else {
                    RegisteredSiswa::where('id', $siswaId)->update([
                        'status_seleksi' => 'tidak_lolos',
                        'diterima_sekolah_id' => null,
                        'diterima_jurusan_id' => null,
                    ]);
                }
            }
        });
    }

    /**
     * Membatalkan status finalisasi seleksi.
     */
    public static function batalkanFinalisasi(): void
    {
        DB::transaction(function () {
            RegisteredSiswa::whereNotNull('pilihan_sekolah_1_id')
                ->whereIn('status_seleksi', ['diterima_pilihan_1', 'diterima_pilihan_2', 'tidak_lolos'])
                ->update([
                    'status_seleksi' => 'menunggu',
                    'diterima_sekolah_id' => null,
                    'diterima_jurusan_id' => null,
                ]);
        });
    }
}
