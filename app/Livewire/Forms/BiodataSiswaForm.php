<?php

namespace App\Livewire\Forms;

use App\Enums\TahunLulus;
use App\Enums\WilayahSekolahAsal;
use App\Models\BiodataSiswa;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BiodataSiswaForm extends Form
{
    #[Validate('required|numeric|digits:10')]
    public string $nisn = '';

    #[Validate('required|numeric|digits:16')]
    public string $nik = '';

    #[Validate(['required', new Enum(WilayahSekolahAsal::class)])]
    public string $wilayahSekolahAsal = '';

    #[Validate(['required', new Enum(TahunLulus::class)])]
    public string $tahunLulus = '';

    public array $biodataDitemukan = [];

    public function searchBiodata(): bool
    {
        $this->validate();

        $result = BiodataSiswa::where('nisn', $this->nisn)
            ->where('nik', $this->nik)
            ->first();

        if (!$result) {
            return false;
        }

        $this->biodataDitemukan = $result->toArray();

        return true;
    }
}
