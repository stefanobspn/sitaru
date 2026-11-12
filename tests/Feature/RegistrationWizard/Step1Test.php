<?php

use App\Livewire\RegistrationWizard;
use App\Models\BiodataSiswa;
use function Pest\Laravel\get;

it('renders successfully', function () {
    get('/daftar')->assertOk();

    Livewire::test(RegistrationWizard::class)
        ->assertSet('currentStep', 1)
        ->assertSet('currentSubStep', 1)
        ->assertSee('NISN')
        ->assertSee('NIK')
        ->assertSee('Tahun Lulus')
        ->assertSee('Wilayah Sekolah Asal');
});

it('validates required fields when searching biodata', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', '')
        ->set('biodata.nik', '')
        ->set('biodata.wilayahSekolahAsal', '')
        ->set('biodata.tahunLulus', '')
        ->call('searchBiodata')
        ->assertHasErrors([
            'biodata.nisn' => 'required',
            'biodata.nik' => 'required',
            'biodata.wilayahSekolahAsal' => 'required',
            'biodata.tahunLulus' => 'required',
        ])
        ->assertSet('currentSubStep', 1);
});

it('validates nisn and nik format and digit length', function (string $nisn, string $nik) {
    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', $nisn)
        ->set('biodata.nik', $nik)
        ->call('searchBiodata')
        ->assertHasErrors(['biodata.nisn', 'biodata.nik'])
        ->assertSet('currentSubStep', 1);
})->with([
    'alphabet input' => ['abcdefghij', 'abcdefghijklmnop'],
    'too short digits' => ['123', '12345'],
    'too long digits' => ['12345678901', '12345678901234567'],
]);

it('shows an error when student biodata is not found in database', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', '1234567890')
        ->set('biodata.nik', '1234567890123456')
        ->set('biodata.wilayahSekolahAsal', 'Dalam Provinsi')
        ->set('biodata.tahunLulus', '2026')
        ->call('searchBiodata')
        ->assertHasErrors('biodata.searchBiodataError')
        ->assertSee('Data tidak ditemukan')
        ->assertSet('currentSubStep', 1);
});

it('finds student biodata and moves to confirmation substep', function () {
    $siswaDummy = BiodataSiswa::factory()->create([
        'nisn' => '0123456789',
        'nik' => '3307123456789012',
        'nama_lengkap' => 'Waguri',
    ]);

    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', $siswaDummy->nisn)
        ->set('biodata.nik', $siswaDummy->nik)
        ->set('biodata.wilayahSekolahAsal', 'Dalam Provinsi')
        ->set('biodata.tahunLulus', '2026')
        ->call('searchBiodata')
        ->assertHasNoErrors()
        ->assertSet('currentSubStep', 2)
        ->assertSee($siswaDummy->nama_lengkap)
        ->assertSee('Konfirmasi');
});
