<?php

use App\Enums\KategoriEventPrestasi;
use App\Enums\NamaKejuaraanPrestasiBerjenjang;
use App\Enums\StatusDomisili;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use App\Enums\TingkatKejuaraanPrestasiBerjenjang;
use App\Livewire\RegistrationWizard;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

// Helper untuk mengisi data lengkap sebelum masuk ke Step 5
function step5Wizard(array $stateOverrides = []): Testable
{
    $testable = Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 5)
        ->set('currentSubStep', 1)
        // Data Biodata
        ->set('biodata.nisn', '1234567890')
        ->set('biodata.nik', '3301234567890001')
        ->set('biodata.tahunLulus', '2026')
        ->set('biodata.biodataDitemukan', [
            'nama_lengkap' => 'Budi Santoso',
            'nama_sekolah_asal' => 'SMP Negeri 1 Surakarta',
            'tempat_lahir' => 'Surakarta',
            'tanggal_lahir' => '2008-05-12',
            'alamat' => 'Jl. Slamet Riyadi No. 12',
            'rt' => '01',
            'rw' => '02',
            'kelurahan' => 'Kerten',
            'kecamatan' => 'Laweyan',
            'kabupaten_kota' => 'Kota Surakarta',
        ])
        // Data Kontak & Domisili
        ->set('kontakDomisili.noWhatsapp', '081234567890')
        ->set('kontakDomisili.email', 'budi@example.com')
        ->set('kontakDomisili.statusDomisili', StatusDomisili::SesuaiKK->value)
        ->set('kontakDomisili.pastedCoordinate', '-7.556123, 110.823456')
        ->set('kontakDomisili.latitude', -7.556123)
        ->set('kontakDomisili.longitude', 110.823456)
        // Data Akademik & Prestasi Default
        ->set('akademikPrestasi.statusKhusus', StatusKhusus::TidakAda->value)
        ->set('akademikPrestasi.statusPrestasi', StatusPrestasi::TidakMemiliki->value)
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', StatusKepengurusanOrganisasi::TidakPernah->value)
        // Data Berkas Default
        ->set('berkas.suratPernyataanKebenaran', \Illuminate\Http\UploadedFile::fake()->create('pernyataan.pdf', 100, 'application/pdf'))
        ->set('berkas.kartuKeluarga', \Illuminate\Http\UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'))
        ->set('berkas.suratKeteranganNilaiRapor', \Illuminate\Http\UploadedFile::fake()->create('rapor.pdf', 100, 'application/pdf'));

    foreach ($stateOverrides as $key => $value) {
        $testable->set($key, $value);
    }

    return $testable;
}

it('renders confirmation page with student data summary', function () {
    step5Wizard()
        // Cek Biodata
        ->assertSee('1234567890')
        ->assertSee('3301234567890001')
        ->assertSee('Budi Santoso')
        ->assertSee('SMP Negeri 1 Surakarta')
        ->assertSee('Kota Surakarta')
        // Cek Kontak & Domisili
        ->assertSee('081234567890')
        ->assertSee('budi@example.com')
        ->assertSee('-7.556123, 110.823456')
        // Cek Tombol Submit
        ->assertSee('Daftar Sekarang');
});

it('renders conditional achievement and organization details when present', function () {
    step5Wizard([
        'akademikPrestasi.statusPrestasi' => StatusPrestasi::Memiliki->value,
        'akademikPrestasi.kategoriEventPrestasi' => KategoriEventPrestasi::Berjenjang->value,
        'akademikPrestasi.tingkatKejuaraanBerjenjang' => TingkatKejuaraanPrestasiBerjenjang::values()[0],
        'akademikPrestasi.namaKejuaraanBerjenjang' => NamaKejuaraanPrestasiBerjenjang::values()[0],
        'akademikPrestasi.statusKepengurusanOrganisasi' => StatusKepengurusanOrganisasi::PernahMengurus->value,
        'akademikPrestasi.jenisOrganisasiJabatan' => 'Ketua OSIS',
    ])
        ->assertSee('Kategori Event')
        ->assertSee(KategoriEventPrestasi::Berjenjang->value)
        ->assertSee(NamaKejuaraanPrestasiBerjenjang::values()[0])
        ->assertSee('Ketua OSIS');
});

it('hides tiered competition fields when student has no achievements', function () {
    step5Wizard([
        'akademikPrestasi.statusPrestasi' => StatusPrestasi::TidakMemiliki->value,
    ])
        ->assertSee(StatusPrestasi::TidakMemiliki->value)
        ->assertDontSee('Tingkat Kejuaraan')
        ->assertDontSee('Nama Kejuaraan');
});

it('navigates back to step 4 when previousStep is called from step 5', function () {
    step5Wizard()
        ->call('previousStep')
        ->assertSet('currentStep', 4)
        ->assertSet('currentSubStep', 1);
});

it('submits registration and advances to step 6', function () {
    step5Wizard()
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 6)
        ->assertSet('currentSubStep', 1);
});

it('fails validation when agreement is not checked', function () {
    step5Wizard()
        ->set('pernyataanSetuju', false)
        ->call('submitPendaftaran')
        ->assertHasErrors(['pernyataanSetuju'])
        ->assertSet('currentStep', 5);
});

it('prevents submission when NISN is already registered', function () {
    \App\Models\RegisteredSiswa::create([
        'nomor_pendaftaran' => 'REG-EXISTING-01',
        'nisn' => '1234567890',
        'nik' => '3301234567890001',
        'nama_lengkap' => 'Existing Siswa',
        'nama_sekolah_asal' => 'SMP Asal',
        'tahun_lulus' => '2026',
        'wilayah_sekolah_asal' => 'dalam_kota',
        'no_whatsapp' => '08123456789',
        'email' => 'existing@example.com',
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

    step5Wizard()
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasErrors(['pernyataanSetuju'])
        ->assertSet('currentStep', 5);
});
