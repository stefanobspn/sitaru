<?php

namespace Database\Factories;

use App\Enums\StatusDomisili;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use App\Enums\TahunLulus;
use App\Enums\WilayahSekolahAsal;
use App\Livewire\Forms\AkademikPrestasiSiswaForm;
use App\Models\RegisteredSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RegisteredSiswa>
 */
class RegisteredSiswaFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterMaking(function (RegisteredSiswa $siswa) {
            if ($siswa->nrtka === null) {
                $siswa->nrtka = $siswa->rata_rata_nilai_rapor ?: fake()->randomFloat(2, 50, 90);
            }
            if ($siswa->nilai_akhir === null) {
                $nrr = (float) ($siswa->rata_rata_nilai_rapor ?? 0);
                $tka = (float) ($siswa->nrtka ?? 0);
                $siswa->nilai_akhir = round((0.5 * $nrr) + (0.5 * $tka), 2);
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nisn = $this->faker->unique()->numerify('00########');

        $nilaiRapor = [];
        $total = 0;
        $count = 0;
        foreach (array_keys(AkademikPrestasiSiswaForm::DAFTAR_MAPEL) as $mapel) {
            for ($sem = 1; $sem <= 5; $sem++) {
                $nilai = 85.0;
                $nilaiRapor[$mapel][$sem] = $nilai;
                $total += $nilai;
                $count++;
            }
        }

        $rataRata = $count > 0 ? round($total / $count, 2) : 0;

        return [
            'nomor_pendaftaran' => 'REG-'.date('Y').'-'.strtoupper(Str::random(6)),
            'biodata_siswa_id' => null,
            'nisn' => $nisn,
            'nik' => $this->faker->unique()->numerify('3307############'),
            'nama_lengkap' => $this->faker->name(),
            'nama_sekolah_asal' => 'SMP Negeri '.fake()->numberBetween(1, 10).' '.fake()->city(),
            'tahun_lulus' => fake()->randomElement(TahunLulus::values()),
            'wilayah_sekolah_asal' => fake()->randomElement(WilayahSekolahAsal::values()),
            'no_whatsapp' => '08'.fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'status_domisili' => StatusDomisili::SesuaiKK->value,
            'tgl_cetak_kk' => fake()->date('Y-m-d', '-1 year'),
            'pasted_coordinate' => '-7.556123, 110.823456',
            'latitude' => -7.556123,
            'longitude' => 110.823456,
            'nilai_rapor' => $nilaiRapor,
            'rata_rata_nilai_rapor' => $rataRata,
            'nrtka' => null,
            'nilai_akhir' => null,
            'status_khusus' => StatusKhusus::TidakAda->value,
            'status_prestasi' => StatusPrestasi::TidakMemiliki->value,
            'kategori_event_prestasi' => null,
            'tingkat_kejuaraan_berjenjang' => null,
            'nama_kejuaraan_berjenjang' => null,
            'peringkat_juara_berjenjang' => null,
            'tahun_perolehan_kejuaraan_berjenjang' => null,
            'penyelenggara_kejuaraan_berjenjang' => null,
            'status_kepengurusan_organisasi' => StatusKepengurusanOrganisasi::TidakPernah->value,
            'jenis_organisasi_jabatan' => null,
            'surat_pernyataan_kebenaran_path' => "berkas-pendaftaran/{$nisn}/pernyataan.pdf",
            'kartu_keluarga_path' => "berkas-pendaftaran/{$nisn}/kk.pdf",
            'surat_keterangan_nilai_rapor_path' => "berkas-pendaftaran/{$nisn}/rapor.pdf",
            'piagam_prestasi_path' => null,
            'sk_organisasi_path' => null,
            'user_id' => null,
            'is_activated' => false,
            'status_verifikasi' => 'menunggu',
            'alasan_penolakan' => null,
            'activation_code' => null,
            'operator_nip' => null,
            'pilihan_sekolah_1_id' => null,
            'pilihan_sekolah_2_id' => null,
            'status_seleksi' => 'belum_memilih',
            'diterima_sekolah_id' => null,
            'is_daftar_ulang' => false,
            'tgl_pilih_sekolah' => null,
        ];
    }
}
