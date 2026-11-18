<?php

use App\Livewire\RegistrationWizard;
use App\Models\RegisteredSiswa;
use Livewire\Livewire;

it('renders step 6 success page with print button', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 6)
        ->set('currentSubStep', 1)
        ->assertSee('Sukses!')
        ->assertSee('Cetak Bukti');
});

it('can trigger cetak bukti pendaftaran action', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 6)
        ->set('currentSubStep', 1)
        ->call('cetakBuktiPendaftaran')
        ->assertHasNoErrors();
});

it('does not allow going back to previous steps once completed at step 6', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 6)
        ->set('currentSubStep', 1)
        ->call('previousStep')
        // Harus tetap di Step 6 (tidak boleh mundur ke Step 5)
        ->assertSet('currentStep', 6)
        ->assertSet('currentSubStep', 1);
});

it('redirects to cetak bukti when registered siswa exists', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nomor_pendaftaran' => 'REG-2026-TEST01',
    ]);

    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 6)
        ->set('currentSubStep', 1)
        ->set('registeredSiswa', $siswa)
        ->call('cetakBuktiPendaftaran')
        ->assertFileDownloaded("bukti-pendaftaran-{$siswa->nomor_pendaftaran}.pdf");
});
