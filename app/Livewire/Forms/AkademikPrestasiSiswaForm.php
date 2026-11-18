<?php

namespace App\Livewire\Forms;

use App\Enums\KategoriEventPrestasi;
use App\Enums\NamaKejuaraanInternasionalBerjenjang;
use App\Enums\NamaKejuaraanPrestasiBerjenjang;
use App\Enums\PeringkatJuaraBerjenjang;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use App\Enums\TingkatKejuaraanPrestasiBerjenjang;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AkademikPrestasiSiswaForm extends Form
{
    public const array DAFTAR_MAPEL = [
        'agama' => 'Pend. Agama dan Budi Pekerti',
        'ppkn' => 'PPKN',
        'indonesia' => 'Bahasa Indonesia',
        'matematika' => 'Matematika',
        'ipa' => 'IPA',
        'ips' => 'IPS',
        'inggris' => 'Bahasa Inggris',
    ];

    #[Validate(['nilaiRapor.*.*' => 'required|numeric|between:0,100'])]
    public array $nilaiRapor = [
        'agama' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
        'ppkn' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
        'indonesia' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
        'matematika' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
        'ipa' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
        'ips' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
        'inggris' => [1 => '', 2 => '', 3 => '', 4 => '', 5 => ''],
    ];

    #[Validate(['required', new Enum(StatusKhusus::class)])]
    public string $statusKhusus = '';

    #[Validate(['required', new Enum(StatusPrestasi::class)])]
    public string $statusPrestasi = '';

    #[Validate('required_if:statusPrestasi,'.StatusPrestasi::Memiliki->value.'|nullable')]
    public string $kategoriEventPrestasi = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::Berjenjang->value.'|nullable')]
    public string $tingkatKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::Berjenjang->value.'|nullable')]
    public string $namaKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::Berjenjang->value.'|nullable')]
    public string $peringkatJuaraBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::Berjenjang->value.'|nullable')]
    public string $tahunPerolehanKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::Berjenjang->value.'|nullable')]
    public string $penyelenggaraKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::TidakBerjenjang->value.'|nullable')]
    public string $tingkatKejuaraanTidakBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::TidakBerjenjang->value.'|nullable')]
    public string $peringkatKurasiTidakBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::TidakBerjenjang->value.'|nullable')]
    public string $namaKejuaraanTidakBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::TidakBerjenjang->value.'|nullable')]
    public string $peringkatJuaraTidakBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::TidakBerjenjang->value.'|nullable')]
    public string $tahunPerolehanKejuaraanTidakBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,'.KategoriEventPrestasi::TidakBerjenjang->value.'|nullable')]
    public string $penyelenggaraKejuaraanTidakBerjenjang = '';

    #[Validate(['required', new Enum(StatusKepengurusanOrganisasi::class)])]
    public string $statusKepengurusanOrganisasi = '';

    #[Validate('required_if:statusKepengurusanOrganisasi,'.StatusKepengurusanOrganisasi::PernahMengurus->value.'|nullable')]
    public string $jenisOrganisasiJabatan = '';

    public function submitRaport(): void
    {
        $rules = Arr::only($this->getRules(), ['nilaiRapor.*.*']);
        $this->validate($rules);
    }

    public function getDaftarNamaKejuaraanBerjenjangOptions(): array
    {
        if ($this->tingkatKejuaraanBerjenjang === TingkatKejuaraanPrestasiBerjenjang::Internasional->value) {
            return NamaKejuaraanInternasionalBerjenjang::values();
        }

        return NamaKejuaraanPrestasiBerjenjang::values();
    }

    public function submitPrestasi(): void
    {
        $this->validate();

        if ($this->statusPrestasi === StatusPrestasi::Memiliki->value
            && $this->kategoriEventPrestasi === KategoriEventPrestasi::Berjenjang->value
            && ! in_array($this->namaKejuaraanBerjenjang, $this->getDaftarNamaKejuaraanBerjenjangOptions(), true)) {
            $this->addError('namaKejuaraanBerjenjang', 'Nama kejuaraan tidak sesuai dengan tingkatan yang dipilih.');
            throw ValidationException::withMessages([
                'akademikPrestasi.namaKejuaraanBerjenjang' => 'Nama kejuaraan tidak sesuai dengan tingkatan yang dipilih.',
            ]);
        }
    }

    public function requiresPiagamPrestasi(): bool
    {
        return $this->statusPrestasi === StatusPrestasi::Memiliki->value;
    }

    public function requiresSkOrganisasi(): bool
    {
        return $this->statusKepengurusanOrganisasi === StatusKepengurusanOrganisasi::PernahMengurus->value;
    }

    public function hitungRataRata(): float
    {
        $totalNilai = 0;
        $count = 0;

        foreach ($this->nilaiRapor as $semesters) {
            foreach ($semesters as $nilai) {
                if ($nilai !== '' && $nilai !== null && is_numeric($nilai)) {
                    $totalNilai += (float) $nilai;
                    $count++;
                }
            }
        }

        return $count > 0 ? round($totalNilai / $count, 2) : 0.0;
    }

    public function hitungNK(): float|string
    {
        if ($this->statusPrestasi !== StatusPrestasi::Memiliki->value) {
            return 0.0;
        }

        if ($this->kategoriEventPrestasi === KategoriEventPrestasi::Berjenjang->value) {
            $tingkat = $this->tingkatKejuaraanBerjenjang;
            $juara = $this->peringkatJuaraBerjenjang;

            if ($tingkat === TingkatKejuaraanPrestasiBerjenjang::Internasional->value) {
                if (in_array($juara, [PeringkatJuaraBerjenjang::JuaraSatu->value, PeringkatJuaraBerjenjang::JuaraDua->value, PeringkatJuaraBerjenjang::JuaraTiga->value], true)) {
                    return 'Langsung diterima';
                }
            }

            if ($tingkat === TingkatKejuaraanPrestasiBerjenjang::Nasional->value) {
                return match ($juara) {
                    PeringkatJuaraBerjenjang::JuaraSatu->value => 'Langsung diterima',
                    PeringkatJuaraBerjenjang::JuaraDua->value => 5.00,
                    PeringkatJuaraBerjenjang::JuaraTiga->value => 4.00,
                    default => 0,
                };
            }

            if ($tingkat === TingkatKejuaraanPrestasiBerjenjang::Provinsi->value) {
                return match ($juara) {
                    PeringkatJuaraBerjenjang::JuaraSatu->value => 3.00,
                    PeringkatJuaraBerjenjang::JuaraDua->value => 2.75,
                    PeringkatJuaraBerjenjang::JuaraTiga->value => 2.50,
                    default => 0,
                };
            }

            if ($tingkat === TingkatKejuaraanPrestasiBerjenjang::KabKota->value) {
                return match ($juara) {
                    PeringkatJuaraBerjenjang::JuaraSatu->value => 2.25,
                    PeringkatJuaraBerjenjang::JuaraDua->value => 2.00,
                    PeringkatJuaraBerjenjang::JuaraTiga->value => 1.75,
                    default => 0.0,
                };
            }

            return 0.0;
        }

        if ($this->kategoriEventPrestasi === KategoriEventPrestasi::TidakBerjenjang->value) {
            $tingkat = $this->tingkatKejuaraanTidakBerjenjang;
            $kurasi = $this->peringkatKurasiTidakBerjenjang;
            $juara = $this->peringkatJuaraTidakBerjenjang;

            $matrix = [
                'Tingkat Internasional' => [
                    'Kurasi: Bintang 5' => ['Juara 1' => 3.00, 'Juara 2' => 2.75, 'Juara 3' => 2.50],
                    'Kurasi: Bintang 4' => ['Juara 1' => 2.97, 'Juara 2' => 2.72, 'Juara 3' => 2.47],
                    'Kurasi: Bintang 3' => ['Juara 1' => 2.94, 'Juara 2' => 2.69, 'Juara 3' => 2.44],
                    'Kurasi: Bintang 2' => ['Juara 1' => 2.91, 'Juara 2' => 2.66, 'Juara 3' => 2.41],
                    'Kurasi: Bintang 1' => ['Juara 1' => 2.88, 'Juara 2' => 2.63, 'Juara 3' => 2.38],
                    'Non Kurasi' => ['Juara 1' => 2.15, 'Juara 2' => 2.12, 'Juara 3' => 2.09],
                ],
                'Tingkat Nasional' => [
                    'Kurasi: Bintang 5' => ['Juara 1' => 2.25, 'Juara 2' => 2.00, 'Juara 3' => 1.75],
                    'Kurasi: Bintang 4' => ['Juara 1' => 2.22, 'Juara 2' => 1.97, 'Juara 3' => 1.72],
                    'Kurasi: Bintang 3' => ['Juara 1' => 2.19, 'Juara 2' => 1.94, 'Juara 3' => 1.69],
                    'Kurasi: Bintang 2' => ['Juara 1' => 2.16, 'Juara 2' => 1.91, 'Juara 3' => 1.66],
                    'Kurasi: Bintang 1' => ['Juara 1' => 2.13, 'Juara 2' => 1.88, 'Juara 3' => 1.63],
                    'Non Kurasi' => ['Juara 1' => 1.40, 'Juara 2' => 1.37, 'Juara 3' => 1.34],
                ],
                'Tingkat Provinsi' => [
                    'Kurasi: Bintang 5' => ['Juara 1' => 1.50, 'Juara 2' => 1.25, 'Juara 3' => 1.00],
                    'Kurasi: Bintang 4' => ['Juara 1' => 1.47, 'Juara 2' => 1.22, 'Juara 3' => 0.97],
                    'Kurasi: Bintang 3' => ['Juara 1' => 1.44, 'Juara 2' => 1.19, 'Juara 3' => 0.94],
                    'Kurasi: Bintang 2' => ['Juara 1' => 1.41, 'Juara 2' => 1.16, 'Juara 3' => 0.91],
                    'Kurasi: Bintang 1' => ['Juara 1' => 1.38, 'Juara 2' => 1.13, 'Juara 3' => 0.88],
                    'Non Kurasi' => ['Juara 1' => 0.65, 'Juara 2' => 0.62, 'Juara 3' => 0.59],
                ],
                'Tingkat Kab/Kota' => [
                    'Kurasi: Bintang 5' => ['Juara 1' => 0.75, 'Juara 2' => 0.50, 'Juara 3' => 0.25],
                    'Kurasi: Bintang 4' => ['Juara 1' => 0.72, 'Juara 2' => 0.47, 'Juara 3' => 0.22],
                    'Kurasi: Bintang 3' => ['Juara 1' => 0.69, 'Juara 2' => 0.44, 'Juara 3' => 0.19],
                    'Kurasi: Bintang 2' => ['Juara 1' => 0.66, 'Juara 2' => 0.41, 'Juara 3' => 0.16],
                    'Kurasi: Bintang 1' => ['Juara 1' => 0.63, 'Juara 2' => 0.38, 'Juara 3' => 0.13],
                    'Non Kurasi' => ['Juara 1' => 0.10, 'Juara 2' => 0.07, 'Juara 3' => 0.04],
                ],
            ];

            return $matrix[$tingkat][$kurasi][$juara] ?? 0.0;
        }

        return 0.0;
    }

    public function hitungNO(): float
    {
        if ($this->statusKepengurusanOrganisasi === StatusKepengurusanOrganisasi::PernahMengurus->value) {
            return 0.75;
        }

        return 0.0;
    }

    public function getFormattedNK(): string
    {
        $nk = $this->hitungNK();

        if (is_string($nk)) {
            return $nk;
        }

        if ($nk == 0) {
            return '0';
        }

        return (string) (float) $nk;
    }

    public function getFormattedNO(): string
    {
        $no = $this->hitungNO();

        if ($no == 0) {
            return '0';
        }

        return (string) (float) $no;
    }

    public function hitungNilaiAkhir(float $nrtka): float|string
    {
        $nk = $this->hitungNK();
        if ($nk === 'Langsung diterima') {
            return 'Langsung diterima';
        }

        $nrr = $this->hitungRataRata();
        $no = $this->hitungNO();

        return round((0.5 * $nrr) + (0.5 * $nrtka) + (float) $nk + $no, 2);
    }
}
