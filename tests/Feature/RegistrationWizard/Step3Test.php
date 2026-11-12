<?php

use App\Enums\JenisOrganisasiJabatan;
use App\Enums\KategoriEventPrestasi;
use App\Enums\NamaKejuaraanPrestasiBerjenjang;
use App\Enums\PenyelenggaraKejuaraanBerjenjang;
use App\Enums\PeringkatJuaraBerjenjang;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use App\Enums\TahunPerolehanKejuaraanBerjenjang;
use App\Enums\TingkatKejuaraanPrestasiBerjenjang;
use App\Livewire\Forms\AkademikPrestasiSiswaForm;
use App\Livewire\RegistrationWizard;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

function generateValidNilaiRapor(): array
{
    $nilai = [];
    foreach (array_keys(AkademikPrestasiSiswaForm::DAFTAR_MAPEL) as $mapel) {
        for ($sem = 1; $sem <= 5; $sem++) {
            $nilai[$mapel][$sem] = '85.00';
        }
    }

    return $nilai;
}

function step3Substep2(): Testable
{
    return Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 2)
        ->set('akademikPrestasi.nilaiRapor', generateValidNilaiRapor());
}

it('validates report card grades must be filled and between 0 and 100', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 1)
        ->set('akademikPrestasi.nilaiRapor.matematika.1', '')
        ->set('akademikPrestasi.nilaiRapor.ipa.2', '105')
        ->call('submitRaport')
        ->assertHasErrors([
            'akademikPrestasi.nilaiRapor.matematika.1' => 'required',
            'akademikPrestasi.nilaiRapor.ipa.2' => 'between',
        ])
        ->assertSet('currentSubStep', 1);
});

it('advances to substep 2 when all report card grades are valid', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 1)
        ->set('akademikPrestasi.nilaiRapor', generateValidNilaiRapor())
        ->call('submitRaport')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2);
});


it('validates main status fields are required in substep 2', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', '')
        ->set('akademikPrestasi.statusPrestasi', '')
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', '')
        ->call('submitPrestasi')
        ->assertHasErrors([
            'akademikPrestasi.statusKhusus' => 'required',
            'akademikPrestasi.statusPrestasi' => 'required',
            'akademikPrestasi.statusKepengurusanOrganisasi' => 'required',
        ])
        ->assertSet('currentStep', 3);
});

it('allows user without achievements and organization to advance to step 4', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::TidakMemiliki->value)
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::TidakPernah->value)
        ->call('submitPrestasi')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 4)
        ->assertSet('currentSubStep', 1);
});

it('requires achievement category when user has achievements', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', '')
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::TidakPernah->value)
        ->call('submitPrestasi')
        ->assertHasErrors(['akademikPrestasi.kategoriEventPrestasi' => 'required_if'])
        ->assertSet('currentStep', 3);
});

it('requires competition details when achievement category is berjenjang', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', KategoriEventPrestasi::Berjenjang->value)
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', '')
        ->set('akademikPrestasi.namaKejuaraanBerjenjang', '')
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::TidakPernah->value)
        ->call('submitPrestasi')
        ->assertHasErrors([
            'akademikPrestasi.tingkatKejuaraanBerjenjang' => 'required_if',
            'akademikPrestasi.namaKejuaraanBerjenjang' => 'required_if',
        ])
        ->assertSet('currentStep', 3);
});

it('requires organization position when user was an organization leader', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::TidakMemiliki->value)
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::PernahMengurus->value)
        ->set('akademikPrestasi.jenisOrganisasiJabatan', '')
        ->call('submitPrestasi')
        ->assertHasErrors(['akademikPrestasi.jenisOrganisasiJabatan' => 'required_if'])
        ->assertSet('currentStep', 3);
});

it('advances to step 4 when user fills all tiered achievement and organization details', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', KategoriEventPrestasi::Berjenjang->value)
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', TingkatKejuaraanPrestasiBerjenjang::values()[0])
        ->set('akademikPrestasi.namaKejuaraanBerjenjang', NamaKejuaraanPrestasiBerjenjang::values()[0])
        ->set('akademikPrestasi.peringkatJuaraBerjenjang', PeringkatJuaraBerjenjang::values()[0])
        ->set('akademikPrestasi.tahunPerolehanKejuaraanBerjenjang', TahunPerolehanKejuaraanBerjenjang::values()[0])
        ->set('akademikPrestasi.penyelenggaraKejuaraanBerjenjang', PenyelenggaraKejuaraanBerjenjang::values()[0])
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::PernahMengurus->value)
        ->set('akademikPrestasi.jenisOrganisasiJabatan', JenisOrganisasiJabatan::values()[0])
        ->call('submitPrestasi')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 4)
        ->assertSet('currentSubStep', 1);
});
