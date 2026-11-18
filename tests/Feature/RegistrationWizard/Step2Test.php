<?php

use App\Enums\StatusDomisili;
use App\Livewire\RegistrationWizard;

it('validates required fields before moving to pinpoint step', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '')
        ->set('kontakDomisili.email', '')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiKK->value)
        ->set('kontakDomisili.tglCetakKk', '')
        ->call('moveToPinPointStep')
        ->assertHasErrors([
            'kontakDomisili.noWhatsapp' => 'required',
            'kontakDomisili.email' => 'required',
            'kontakDomisili.tglCetakKk' => 'required_if',
        ])
        ->assertSet('currentSubStep', 1);
});

it('validates contact formats', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', 'bukan-angka')
        ->set('kontakDomisili.email', 'email-tidak-valid')
        ->call('moveToPinPointStep')
        ->assertHasErrors([
            'kontakDomisili.noWhatsapp' => 'numeric',
            'kontakDomisili.email' => 'email',
        ])
        ->assertSet('currentSubStep', 1);
});

it('advances to pinpoint substep when contact details are valid', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiKK->value)
        ->set('kontakDomisili.tglCetakKk', '2022-01-15')
        ->call('moveToPinPointStep')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2)
        ->assertSee('Pinpoint Koordinat Rumah');
});

it('rejects invalid coordinate formats and does not dispatch map event', function (string $invalidCoordinate) {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 2)
        ->set('kontakDomisili.pastedCoordinate', $invalidCoordinate)
        ->call('renderCoordinate')
        ->assertHasErrors(['kontakDomisili.pastedCoordinate'])
        ->assertNotDispatched('render-map');
})->with([
    'random text' => ['sembarang teks'],
    'missing comma' => ['-7.556123 110.823456'],
    'latitude out of range' => ['-95.123456, 110.823456'],
    'longitude out of range' => ['-7.556123, 195.823456'],
]);

it('parses valid coordinates and dispatches render-map event', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 2)
        ->set('kontakDomisili.pastedCoordinate', '-7.556123, 110.823456')
        ->call('renderCoordinate')
        ->assertHasNoErrors()
        ->assertSet('kontakDomisili.latitude', -7.556123)
        ->assertSet('kontakDomisili.longitude', 110.823456)
        ->assertDispatched('render-map', lat: -7.556123, lng: 110.823456);
});

it('moves to step 3 when confirmation button is clicked', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 2)
        ->set('kontakDomisili.pastedCoordinate', '-7.556123, 110.823456')
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        ->assertSet('currentSubStep', 1);
});

it('validates required fields for SesuaiMutasiOrtuWali before moving to pinpoint step', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiMutasiOrtuWali->value)
        ->set('kontakDomisili.namaInstansiMutasi', '')
        ->set('kontakDomisili.nomorSuratPenugasan', '')
        ->set('kontakDomisili.tglSuratPenugasan', '')
        ->set('kontakDomisili.nomorSkDomisili', '')
        ->call('moveToPinPointStep')
        ->assertHasErrors([
            'kontakDomisili.namaInstansiMutasi' => 'required_if',
            'kontakDomisili.nomorSuratPenugasan' => 'required_if',
            'kontakDomisili.tglSuratPenugasan' => 'required_if',
            'kontakDomisili.nomorSkDomisili' => 'required_if',
        ])
        ->assertSet('currentSubStep', 1);
});

it('validates required fields for SesuaiDomisiliPondok before moving to pinpoint step', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiDomisiliPondok->value)
        ->set('kontakDomisili.namaPondokPesantren', '')
        ->set('kontakDomisili.nomorIzinPondokEmis', '')
        ->set('kontakDomisili.jenjangPondok', '')
        ->call('moveToPinPointStep')
        ->assertHasErrors([
            'kontakDomisili.namaPondokPesantren' => 'required_if',
            'kontakDomisili.nomorIzinPondokEmis' => 'required_if',
            'kontakDomisili.jenjangPondok' => 'required_if',
        ])
        ->assertSet('currentSubStep', 1);
});

it('validates required fields for SesuaiDomisiliPantiAsuhan before moving to pinpoint step', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
        ->set('kontakDomisili.namaPantiAsuhan', '')
        ->set('kontakDomisili.nomorRegistrasiDinsos', '')
        ->set('kontakDomisili.prioritasPanti', '')
        ->call('moveToPinPointStep')
        ->assertHasErrors([
            'kontakDomisili.namaPantiAsuhan' => 'required_if',
            'kontakDomisili.nomorRegistrasiDinsos' => 'required_if',
            'kontakDomisili.prioritasPanti' => 'required_if',
        ])
        ->assertSet('currentSubStep', 1);
});

it('advances to pinpoint substep when SesuaiMutasiOrtuWali fields are valid', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiMutasiOrtuWali->value)
        ->set('kontakDomisili.namaInstansiMutasi', 'PT PLN Persero')
        ->set('kontakDomisili.nomorSuratPenugasan', 'SK/123/2026')
        ->set('kontakDomisili.tglSuratPenugasan', '2026-01-10')
        ->set('kontakDomisili.nomorSkDomisili', 'SKD/456/2026')
        ->set('kontakDomisili.isAnakGuru', true)
        ->call('moveToPinPointStep')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2);
});

it('advances to pinpoint substep when SesuaiDomisiliPondok fields are valid', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiDomisiliPondok->value)
        ->set('kontakDomisili.namaPondokPesantren', 'Ponpes Al-Huda')
        ->set('kontakDomisili.nomorIzinPondokEmis', 'EMIS-987654')
        ->set('kontakDomisili.jenjangPondok', 'PKPPS')
        ->call('moveToPinPointStep')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2);
});

it('allows empty tglCetakKk when statusDomisili is not SesuaiKK but other fields are valid', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'siswa@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
        ->set('kontakDomisili.tglCetakKk', '')
        ->set('kontakDomisili.namaPantiAsuhan', 'Panti Asuhan Kasih Ibu')
        ->set('kontakDomisili.nomorRegistrasiDinsos', 'REG-DS-12345')
        ->set('kontakDomisili.prioritasPanti', 'Prioritas 1 (P1)')
        ->call('moveToPinPointStep')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2);
});

it('navigates back from step 2 substep 2 to step 2 substep 1', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 2)
        ->call('previousStep')
        ->assertSet('currentStep', 2)
        ->assertSet('currentSubStep', 1);
});

it('navigates back from step 2 substep 1 to step 1 substep 2', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 2)
        ->set('currentSubStep', 1)
        ->call('previousStep')
        ->assertSet('currentStep', 1)
        ->assertSet('currentSubStep', 2);
});
