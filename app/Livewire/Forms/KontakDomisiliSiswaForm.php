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

    #[Validate('required_if:statusDomisili,' . StatusDomisili::SesuaiKK->value . '|nullable|date')]
    public string $tglCetakKk = '';

    #[Validate(['required', 'string', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/'])]
    public string $pastedCoordinate = '';

    public ?float $latitude = null;
    public ?float $longitude = null;

    public function validateBeforeMoveToPinPointStep(): void
    {
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
