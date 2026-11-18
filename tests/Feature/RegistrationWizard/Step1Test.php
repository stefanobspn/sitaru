<?php

use App\Livewire\RegistrationWizard;
use App\Models\BiodataSiswa;
use App\Models\RegisteredSiswa;
use Carbon\Carbon;

use function Pest\Laravel\get;

beforeEach(function () {
    Carbon::setTestNow('2026-06-05 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

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
        ->assertSee('Konfirmasi')
        ->assertSee(Carbon::parse($siswaDummy->tanggal_lahir)->locale('id')->translatedFormat('d F Y'))
        ->assertDontSee('T00:00:00.000000Z');
});

it('redirects to index route when previousStep is called on step 1 substep 1', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 1)
        ->set('currentSubStep', 1)
        ->call('previousStep')
        ->assertRedirect(route('index'));
});

it('navigates back to substep 1 when previousStep is called on step 1 substep 2', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 1)
        ->set('currentSubStep', 2)
        ->call('previousStep')
        ->assertSet('currentStep', 1)
        ->assertSet('currentSubStep', 1);
});

it('validates invalid enum values for wilayah sekolah asal and tahun lulus', function () {
    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', '1234567890')
        ->set('biodata.nik', '1234567890123456')
        ->set('biodata.wilayahSekolahAsal', 'wilayah-tidak-valid')
        ->set('biodata.tahunLulus', '1990')
        ->call('searchBiodata')
        ->assertHasErrors(['biodata.wilayahSekolahAsal', 'biodata.tahunLulus'])
        ->assertSet('currentSubStep', 1);
});

it('shows an error when student NISN is already registered in SPMB', function () {
    RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
    ]);

    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', '1234567890')
        ->set('biodata.nik', '1234567890123456')
        ->set('biodata.wilayahSekolahAsal', 'Dalam Provinsi')
        ->set('biodata.tahunLulus', '2026')
        ->call('searchBiodata')
        ->assertHasErrors('biodata.searchBiodataError')
        ->assertSee('Siswa dengan NISN ini sudah pernah didaftarkan')
        ->assertSet('currentSubStep', 1);
});

it('prevents registration on 15 June 2026 when registration schedule has ended', function () {
    Carbon::setTestNow('2026-06-15 10:00:00');

    get('/daftar')
        ->assertOk()
        ->assertSee('Tahap Pengajuan Akun Telah Ditutup')
        ->assertSee('telah ditutup pada tanggal 12 Juni 2026');

    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', '1234567890')
        ->set('biodata.nik', '1234567890123456')
        ->set('biodata.wilayahSekolahAsal', 'Dalam Provinsi')
        ->set('biodata.tahunLulus', '2026')
        ->call('searchBiodata')
        ->assertHasErrors('biodata.searchBiodataError')
        ->assertSee('telah ditutup pada tanggal 12 Juni 2026')
        ->assertSet('currentSubStep', 1);
});

it('prevents registration before 3 June 2026 when schedule has not started', function () {
    Carbon::setTestNow('2026-06-01 10:00:00');

    get('/daftar')
        ->assertOk()
        ->assertSee('Tahap Pengajuan Akun Belum Dibuka')
        ->assertSee('belum dibuka');

    Livewire::test(RegistrationWizard::class)
        ->set('biodata.nisn', '1234567890')
        ->set('biodata.nik', '1234567890123456')
        ->set('biodata.wilayahSekolahAsal', 'Dalam Provinsi')
        ->set('biodata.tahunLulus', '2026')
        ->call('searchBiodata')
        ->assertHasErrors('biodata.searchBiodataError')
        ->assertSee('belum dibuka')
        ->assertSet('currentSubStep', 1);
});
