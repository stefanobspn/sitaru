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
