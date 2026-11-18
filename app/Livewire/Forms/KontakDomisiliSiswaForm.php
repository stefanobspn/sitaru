<?php

namespace App\Livewire\Forms;

use App\Enums\StatusDomisili;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

class KontakDomisiliSiswaForm extends Form
{
    #[Validate('required|numeric')]
    public string $noWhatsapp = '';

    #[Validate(['required', 'email', 'max:255'])]
    public string $email = '';

    #[Validate(['required', new Enum(StatusDomisili::class)])]
    public string $statusDomisili = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiKK->value.'|nullable|date')]
    public string $tglCetakKk = '';

    // Sesuai Mutasi Ortu/Wali
    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiMutasiOrtuWali->value.'|nullable|string|max:255')]
    public string $namaInstansiMutasi = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiMutasiOrtuWali->value.'|nullable|string|max:255')]
    public string $nomorSuratPenugasan = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiMutasiOrtuWali->value.'|nullable|date')]
    public string $tglSuratPenugasan = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiMutasiOrtuWali->value.'|nullable|string|max:255')]
    public string $nomorSkDomisili = '';

    #[Validate('boolean')]
    public bool $isAnakGuru = false;

    // Sesuai Domisili Pondok (EMIS)
    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiDomisiliPondok->value.'|nullable|string|max:255')]
    public string $namaPondokPesantren = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiDomisiliPondok->value.'|nullable|string|max:255')]
    public string $nomorIzinPondokEmis = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiDomisiliPondok->value.'|nullable|string')]
    public string $jenjangPondok = '';

    // Sesuai Domisili Panti Asuhan (Dinas Sosial)
    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiDomisiliPantiAsuhan->value.'|nullable|string|max:255')]
    public string $namaPantiAsuhan = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiDomisiliPantiAsuhan->value.'|nullable|string|max:255')]
    public string $nomorRegistrasiDinsos = '';

    #[Validate('required_if:statusDomisili,'.StatusDomisili::SesuaiDomisiliPantiAsuhan->value.'|nullable|string')]
    public string $prioritasPanti = '';

    #[Validate(['required', 'string', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/'])]
    public string $pastedCoordinate = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public function resetUnusedDomisiliFields(): void
    {
        if ($this->statusDomisili !== StatusDomisili::SesuaiKK->value) {
            $this->tglCetakKk = '';
        }

        if ($this->statusDomisili !== StatusDomisili::SesuaiMutasiOrtuWali->value) {
            $this->namaInstansiMutasi = '';
            $this->nomorSuratPenugasan = '';
            $this->tglSuratPenugasan = '';
            $this->nomorSkDomisili = '';
            $this->isAnakGuru = false;
        }

        if ($this->statusDomisili !== StatusDomisili::SesuaiDomisiliPondok->value) {
            $this->namaPondokPesantren = '';
            $this->nomorIzinPondokEmis = '';
            $this->jenjangPondok = '';
        }

        if ($this->statusDomisili !== StatusDomisili::SesuaiDomisiliPantiAsuhan->value) {
            $this->namaPantiAsuhan = '';
            $this->nomorRegistrasiDinsos = '';
            $this->prioritasPanti = '';
        }
    }

    public function validateBeforeMoveToPinPointStep(): void
    {
        $this->resetUnusedDomisiliFields();
        $rules = Arr::except($this->getRules(), ['pastedCoordinate']);
        $this->validate($rules);
    }

    public function renderCoordinate(): void
    {
        $this->validateOnly('pastedCoordinate');

        [$lat, $lng] = array_map('trim', explode(',', $this->pastedCoordinate));
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;
    }
}
