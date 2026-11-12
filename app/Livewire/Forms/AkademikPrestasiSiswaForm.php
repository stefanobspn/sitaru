<?php

namespace App\Livewire\Forms;

use App\Enums\JenisOrganisasiJabatan;
use App\Enums\KategoriEventPrestasi;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;
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

    #[Validate('required_if:statusPrestasi,' . StatusPrestasi::Memiliki->value . '|nullable')]
    public string $kategoriEventPrestasi = '';

    #[Validate('required_if:kategoriEventPrestasi,' . KategoriEventPrestasi::Berjenjang->value . '|nullable')]
    public string $tingkatKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,' . KategoriEventPrestasi::Berjenjang->value . '|nullable')]
    public string $namaKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,' . KategoriEventPrestasi::Berjenjang->value . '|nullable')]
    public string $peringkatJuaraBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,' . KategoriEventPrestasi::Berjenjang->value . '|nullable')]
    public string $tahunPerolehanKejuaraanBerjenjang = '';

    #[Validate('required_if:kategoriEventPrestasi,' . KategoriEventPrestasi::Berjenjang->value . '|nullable')]
    public string $penyelenggaraKejuaraanBerjenjang = '';

    #[Validate(['required', new Enum(StatusKepengurusanOrganisasi::class)])]
    public string $statusKepengurusanOrganisasi = '';

    #[Validate('required_if:statusKepengurusanOrganisasi,' . StatusKepengurusanOrganisasi::PernahMengurus->value . '|nullable')]
    public string $jenisOrganisasiJabatan = '';

    public function submitRaport(): void
    {
        $rules = Arr::only($this->getRules(), ['nilaiRapor.*.*']);
        $this->validate($rules);
    }

    public function submitPrestasi(): void
    {
        $this->validate();
    }

    public function requiresPiagamPrestasi(): bool
    {
        return $this->statusPrestasi === StatusPrestasi::Memiliki->value;
    }

    public function requiresSkOrganisasi(): bool
    {
        return $this->statusKepengurusanOrganisasi === StatusKepengurusanOrganisasi::PernahMengurus->value;
    }
}
