<?php

use App\Livewire\RegistrationWizard;
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
    $siswa = \App\Models\RegisteredSiswa::create([
        'nomor_pendaftaran' => 'REG-2026-TEST01',
        'nisn' => '1234567899',
        'nik' => '3301234567890002',
        'nama_lengkap' => 'Budi Santoso',
        'nama_sekolah_asal' => 'SMP Asal',
        'tahun_lulus' => '2026',
        'wilayah_sekolah_asal' => 'dalam_kota',
        'no_whatsapp' => '08123456789',
        'email' => 'budi@example.com',
        'status_domisili' => 'sesuai_kk',
        'pasted_coordinate' => '-7.556, 110.823',
        'latitude' => -7.556,
        'longitude' => 110.823,
        'nilai_rapor' => [],
        'rata_rata_nilai_rapor' => 85.0,
        'status_khusus' => 'tidak_ada',
        'status_prestasi' => 'tidak_memiliki',
        'status_kepengurusan_organisasi' => 'tidak_pernah',
        'surat_pernyataan_kebenaran_path' => 'fake.pdf',
        'kartu_keluarga_path' => 'fake.pdf',
        'surat_keterangan_nilai_rapor_path' => 'fake.pdf',
        'is_activated' => false,
    ]);

    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 6)
        ->set('currentSubStep', 1)
        ->set('registeredSiswa', $siswa)
        ->call('cetakBuktiPendaftaran')
        ->assertRedirect(route('pendaftaran.cetak-bukti', ['nomor' => 'REG-2026-TEST01']));
});
