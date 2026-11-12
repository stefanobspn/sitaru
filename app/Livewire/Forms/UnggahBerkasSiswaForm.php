<?php

namespace App\Livewire\Forms;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class UnggahBerkasSiswaForm extends Form
{
    public ?TemporaryUploadedFile $suratPernyataanKebenaran = null;
    public ?TemporaryUploadedFile $kartuKeluarga = null;
    public ?TemporaryUploadedFile $suratKeteranganNilaiRapor = null;

    // conditional
    public ?TemporaryUploadedFile $piagamPrestasi = null;
    public ?TemporaryUploadedFile $skOrganisasi = null;

    public function rules(): array
    {
        $baseFileRule = ['required', 'file', 'mimes:pdf', 'max:1024'];

        $rules = [
            'suratPernyataanKebenaran' => $baseFileRule,
            'kartuKeluarga' => $baseFileRule,
            'suratKeteranganNilaiRapor' => $baseFileRule,
        ];

        $akademik = $this->component->akademikPrestasi;

        if ($akademik->requiresPiagamPrestasi()) {
            $rules['piagamPrestasi'] = $baseFileRule;
        }
        if ($akademik->requiresSkOrganisasi()) {
            $rules['skOrganisasi'] = $baseFileRule;
        }
        return $rules;
    }

    public function submitBerkas(): void
    {
        $this->validate();
    }
}
