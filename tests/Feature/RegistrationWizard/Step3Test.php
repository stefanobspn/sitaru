<?php

use App\Enums\JenisOrganisasiJabatan;
use App\Enums\KategoriEventPrestasi;
use App\Enums\NamaKejuaraanInternasionalBerjenjang;
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
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', TingkatKejuaraanPrestasiBerjenjang::Internasional->value)
        ->set('akademikPrestasi.namaKejuaraanBerjenjang', NamaKejuaraanInternasionalBerjenjang::values()[0])
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

it('accepts boundary values and decimal numbers for report card grades', function () {
    $nilai = generateValidNilaiRapor();
    $nilai['matematika'][1] = '0';
    $nilai['ipa'][2] = '100';
    $nilai['bahasa_indonesia'][3] = '87.75';

    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 1)
        ->set('akademikPrestasi.nilaiRapor', $nilai)
        ->call('submitRaport')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2);
});

it('correctly determines whether piagam prestasi and sk organisasi are required', function () {
    $component = new RegistrationWizard;
    $form = new AkademikPrestasiSiswaForm($component, 'akademikPrestasi');

    $form->statusPrestasi = StatusPrestasi::TidakMemiliki->value;
    expect($form->requiresPiagamPrestasi())->toBeFalse();

    $form->statusPrestasi = StatusPrestasi::Memiliki->value;
    expect($form->requiresPiagamPrestasi())->toBeTrue();

    $form->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::TidakPernah->value;
    expect($form->requiresSkOrganisasi())->toBeFalse();

    $form->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::PernahMengurus->value;
    expect($form->requiresSkOrganisasi())->toBeTrue();
});

it('navigates back from step 3 substep 2 to step 3 substep 1', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 2)
        ->call('previousStep')
        ->assertSet('currentStep', 3)
        ->assertSet('currentSubStep', 1);
});

it('navigates back from step 3 substep 1 to step 2 substep 2', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 1)
        ->call('previousStep')
        ->assertSet('currentStep', 2)
        ->assertSet('currentSubStep', 2);
});

it('calculates average report card grades (NRR) correctly and displays it dynamically in step 3', function () {
    $component = new RegistrationWizard;
    $form = new AkademikPrestasiSiswaForm($component, 'akademikPrestasi');

    expect($form->hitungRataRata())->toBe(0.0);

    $form->nilaiRapor = generateValidNilaiRapor();
    expect($form->hitungRataRata())->toBe(85.0);

    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 1)
        ->set('akademikPrestasi.nilaiRapor', generateValidNilaiRapor())
        ->assertSee('Nilai Rata-Rata Rapor (NRR)')
        ->assertSee('85.00');
});

it('requires competition details when achievement category is tidak berjenjang', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', KategoriEventPrestasi::TidakBerjenjang->value)
        ->set('akademikPrestasi.tingkatKejuaraanTidakBerjenjang', '')
        ->set('akademikPrestasi.peringkatKurasiTidakBerjenjang', '')
        ->set('akademikPrestasi.namaKejuaraanTidakBerjenjang', '')
        ->set('akademikPrestasi.peringkatJuaraTidakBerjenjang', '')
        ->set('akademikPrestasi.tahunPerolehanKejuaraanTidakBerjenjang', '')
        ->set('akademikPrestasi.penyelenggaraKejuaraanTidakBerjenjang', '')
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::TidakPernah->value)
        ->call('submitPrestasi')
        ->assertHasErrors([
            'akademikPrestasi.tingkatKejuaraanTidakBerjenjang' => 'required_if',
            'akademikPrestasi.peringkatKurasiTidakBerjenjang' => 'required_if',
            'akademikPrestasi.namaKejuaraanTidakBerjenjang' => 'required_if',
            'akademikPrestasi.peringkatJuaraTidakBerjenjang' => 'required_if',
            'akademikPrestasi.tahunPerolehanKejuaraanTidakBerjenjang' => 'required_if',
            'akademikPrestasi.penyelenggaraKejuaraanTidakBerjenjang' => 'required_if',
        ])
        ->assertSet('currentStep', 3);
});

it('calculates NK and NO correctly based on Juknis tables', function () {
    $component = new RegistrationWizard;
    $form = new AkademikPrestasiSiswaForm($component, 'akademikPrestasi');

    // Tanpa prestasi
    $form->statusPrestasi = StatusPrestasi::TidakMemiliki->value;
    expect($form->hitungNK())->toBe(0.0);

    // Berjenjang Tingkat Nasional Juara 2 => 5.00
    $form->statusPrestasi = StatusPrestasi::Memiliki->value;
    $form->kategoriEventPrestasi = KategoriEventPrestasi::Berjenjang->value;
    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::Nasional->value;
    $form->peringkatJuaraBerjenjang = PeringkatJuaraBerjenjang::JuaraDua->value;
    expect($form->hitungNK())->toBe(5.00);

    // Berjenjang Tingkat Nasional Juara 1 => Langsung diterima
    $form->peringkatJuaraBerjenjang = PeringkatJuaraBerjenjang::JuaraSatu->value;
    expect($form->hitungNK())->toBe('Langsung diterima');

    // Berjenjang Tingkat Kab/Kota Juara 3 => 1.75
    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::KabKota->value;
    $form->peringkatJuaraBerjenjang = PeringkatJuaraBerjenjang::JuaraTiga->value;
    expect($form->hitungNK())->toBe(1.75);

    // Tidak Berjenjang Nasional Bintang 5 Juara 1 => 2.25
    $form->kategoriEventPrestasi = KategoriEventPrestasi::TidakBerjenjang->value;
    $form->tingkatKejuaraanTidakBerjenjang = TingkatKejuaraanPrestasiBerjenjang::Nasional->value;
    $form->peringkatKurasiTidakBerjenjang = 'Kurasi: Bintang 5';
    $form->peringkatJuaraTidakBerjenjang = PeringkatJuaraBerjenjang::JuaraSatu->value;
    expect($form->hitungNK())->toBe(2.25);

    // Tidak Berjenjang Non Kurasi Kab/Kota Juara 3 => 0.04
    $form->tingkatKejuaraanTidakBerjenjang = TingkatKejuaraanPrestasiBerjenjang::KabKota->value;
    $form->peringkatKurasiTidakBerjenjang = 'Non Kurasi';
    $form->peringkatJuaraTidakBerjenjang = PeringkatJuaraBerjenjang::JuaraTiga->value;
    expect($form->hitungNK())->toBe(0.04);

    // Organisasi: Tidak pernah => 0
    $form->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::TidakPernah->value;
    expect($form->hitungNO())->toBe(0.0);

    // Organisasi: Pernah mengurus => 0.75
    $form->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::PernahMengurus->value;
    expect($form->hitungNO())->toBe(0.75);
});

it('dynamically displays NK and NO in step 3 substep 2', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 2)
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', KategoriEventPrestasi::Berjenjang->value)
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', TingkatKejuaraanPrestasiBerjenjang::Nasional->value)
        ->set('akademikPrestasi.peringkatJuaraBerjenjang', PeringkatJuaraBerjenjang::JuaraDua->value)
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::PernahMengurus->value)
        ->assertSee('Nilai Prestasi/Kejuaraan (NK): 5')
        ->assertSee('Nilai Organisasi (NO): 0.75');
});

it('provides international competition options when international tier is selected', function () {
    $component = new RegistrationWizard;
    $form = new AkademikPrestasiSiswaForm($component, 'akademikPrestasi');

    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::Internasional->value;
    expect($form->getDaftarNamaKejuaraanBerjenjangOptions())->toBe(NamaKejuaraanInternasionalBerjenjang::values());
});

it('provides national competition options when national or regional tier is selected', function () {
    $component = new RegistrationWizard;
    $form = new AkademikPrestasiSiswaForm($component, 'akademikPrestasi');

    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::Nasional->value;
    expect($form->getDaftarNamaKejuaraanBerjenjangOptions())->toBe(NamaKejuaraanPrestasiBerjenjang::values());

    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::Provinsi->value;
    expect($form->getDaftarNamaKejuaraanBerjenjangOptions())->toBe(NamaKejuaraanPrestasiBerjenjang::values());

    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::KabKota->value;
    expect($form->getDaftarNamaKejuaraanBerjenjangOptions())->toBe(NamaKejuaraanPrestasiBerjenjang::values());
});

it('rejects mismatched competition names for chosen tier', function () {
    step3Substep2()
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::values()[0])
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', KategoriEventPrestasi::Berjenjang->value)
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', TingkatKejuaraanPrestasiBerjenjang::Internasional->value)
        ->set('akademikPrestasi.namaKejuaraanBerjenjang', NamaKejuaraanPrestasiBerjenjang::values()[0]) // National competition, not international
        ->set('akademikPrestasi.peringkatJuaraBerjenjang', PeringkatJuaraBerjenjang::values()[0])
        ->set('akademikPrestasi.tahunPerolehanKejuaraanBerjenjang', TahunPerolehanKejuaraanBerjenjang::values()[0])
        ->set('akademikPrestasi.penyelenggaraKejuaraanBerjenjang', PenyelenggaraKejuaraanBerjenjang::values()[0])
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::TidakPernah->value)
        ->call('submitPrestasi')
        ->assertHasErrors(['akademikPrestasi.namaKejuaraanBerjenjang'])
        ->assertSet('currentStep', 3);
});

it('resets selected competition name when tier changes and selection is no longer valid', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 3)
        ->set('currentSubStep', 2)
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::Memiliki->value)
        ->set('akademikPrestasi.kategoriEventPrestasi', KategoriEventPrestasi::Berjenjang->value)
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', TingkatKejuaraanPrestasiBerjenjang::Internasional->value)
        ->set('akademikPrestasi.namaKejuaraanBerjenjang', NamaKejuaraanInternasionalBerjenjang::values()[0])
        ->assertSet('akademikPrestasi.namaKejuaraanBerjenjang', NamaKejuaraanInternasionalBerjenjang::values()[0])
        ->set('akademikPrestasi.tingkatKejuaraanBerjenjang', TingkatKejuaraanPrestasiBerjenjang::Nasional->value)
        ->assertSet('akademikPrestasi.namaKejuaraanBerjenjang', '');
});

it('calculates nilai akhir with 50% NRR, 50% NRTKA, NK, and NO', function () {
    $component = new RegistrationWizard;
    $form = new AkademikPrestasiSiswaForm($component, 'akademikPrestasi');

    $form->nilaiRapor = generateValidNilaiRapor();

    // Tanpa prestasi, tanpa organisasi: NA = (0.5 * 85) + (0.5 * 75) = 42.5 + 37.5 = 80.00
    $form->statusPrestasi = StatusPrestasi::TidakMemiliki->value;
    $form->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::TidakPernah->value;
    expect($form->hitungNilaiAkhir(75.00))->toBe(80.00);

    // Dengan organisasi (0.75): NA = 80.00 + 0.75 = 80.75
    $form->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::PernahMengurus->value;
    expect($form->hitungNilaiAkhir(75.00))->toBe(80.75);

    // Dengan kejuaraan (misal Kab/Kota Juara 1 = 2.25): NA = 80.75 + 2.25 = 83.00
    $form->statusPrestasi = StatusPrestasi::Memiliki->value;
    $form->kategoriEventPrestasi = KategoriEventPrestasi::Berjenjang->value;
    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::KabKota->value;
    $form->peringkatJuaraBerjenjang = PeringkatJuaraBerjenjang::JuaraSatu->value;
    expect($form->hitungNilaiAkhir(75.00))->toBe(83.00);

    // Jika Langsung diterima: return 'Langsung diterima'
    $form->tingkatKejuaraanBerjenjang = TingkatKejuaraanPrestasiBerjenjang::Nasional->value;
    $form->peringkatJuaraBerjenjang = PeringkatJuaraBerjenjang::JuaraSatu->value;
    expect($form->hitungNilaiAkhir(75.00))->toBe('Langsung diterima');
});
