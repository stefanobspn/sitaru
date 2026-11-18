<?php

use App\Enums\KategoriEventPrestasi;
use App\Enums\NamaKejuaraanPrestasiBerjenjang;
use App\Enums\StatusDomisili;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use App\Enums\TingkatKejuaraanPrestasiBerjenjang;
use App\Livewire\Forms\AkademikPrestasiSiswaForm;
use App\Livewire\RegistrationWizard;
use App\Models\RegisteredSiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

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
            'nrtka' => 80.00,
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
        ->set('berkas.suratPernyataanKebenaran', UploadedFile::fake()->create('pernyataan.pdf', 100, 'application/pdf'))
        ->set('berkas.kartuKeluarga', UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'))
        ->set('berkas.suratKeteranganNilaiRapor', UploadedFile::fake()->create('rapor.pdf', 100, 'application/pdf'));

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
        'akademikPrestasi.tingkatKejuaraanBerjenjang' => TingkatKejuaraanPrestasiBerjenjang::Nasional->value,
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

it('submits registration, persists to database, stores uploaded files, and advances to step 6', function () {
    Storage::fake('public');

    $nilaiRapor = [];
    foreach (array_keys(AkademikPrestasiSiswaForm::DAFTAR_MAPEL) as $mapel) {
        for ($sem = 1; $sem <= 5; $sem++) {
            $nilaiRapor[$mapel][$sem] = 80;
        }
    }

    $wizard = step5Wizard([
        'akademikPrestasi.nilaiRapor' => $nilaiRapor,
    ]);

    $wizard->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 6)
        ->assertSet('currentSubStep', 1);

    assertDatabaseHas('registered_siswas', [
        'nisn' => '1234567890',
        'nik' => '3301234567890001',
        'nama_lengkap' => 'Budi Santoso',
        'rata_rata_nilai_rapor' => 80.00,
        'nrtka' => 80.00,
        'nilai_akhir' => 80.00,
        'is_activated' => false,
    ]);

    $siswa = RegisteredSiswa::where('nisn', '1234567890')->first();
    expect($siswa)->not->toBeNull()
        ->and($siswa->nomor_pendaftaran)->toMatch('/^REG-\d{4}-[A-Z0-9]{6}$/');

    Storage::disk('public')->assertExists($siswa->surat_pernyataan_kebenaran_path);
    Storage::disk('public')->assertExists($siswa->kartu_keluarga_path);
    Storage::disk('public')->assertExists($siswa->surat_keterangan_nilai_rapor_path);
});

it('renders coordinates automatically if latitude and longitude are null but pastedCoordinate is filled', function () {
    Storage::fake('public');

    $wizard = step5Wizard([
        'kontakDomisili.latitude' => null,
        'kontakDomisili.longitude' => null,
        'kontakDomisili.pastedCoordinate' => '-7.556123, 110.823456',
    ]);

    $wizard->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasNoErrors();

    assertDatabaseHas('registered_siswas', [
        'nisn' => '1234567890',
        'latitude' => -7.556123,
        'longitude' => 110.823456,
    ]);
});

it('fails validation when agreement is not checked', function () {
    step5Wizard()
        ->set('pernyataanSetuju', false)
        ->call('submitPendaftaran')
        ->assertHasErrors(['pernyataanSetuju'])
        ->assertSet('currentStep', 5);
});

it('prevents submission when NISN is already registered', function () {
    RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
    ]);

    step5Wizard()
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasErrors(['pernyataanSetuju' => 'Siswa dengan NISN ini sudah pernah didaftarkan.'])
        ->assertSet('currentStep', 5);
});

it('handles database exception gracefully during registration submission', function () {
    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new Exception('Database failure'));

    step5Wizard()
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasErrors(['pernyataanSetuju' => 'Terjadi kesalahan saat memproses pendaftaran. Silakan coba lagi.'])
        ->assertSet('currentStep', 5);
});

it('renders and persists conditional domisili details for SesuaiMutasiOrtuWali', function () {
    Storage::fake('public');

    $wizard = step5Wizard([
        'biodata.nisn' => '9988776655',
        'kontakDomisili.statusDomisili' => StatusDomisili::SesuaiMutasiOrtuWali->value,
        'kontakDomisili.namaInstansiMutasi' => 'PT Semen Gresik',
        'kontakDomisili.nomorSuratPenugasan' => 'SK-MUTASI-001',
        'kontakDomisili.tglSuratPenugasan' => '2026-02-15',
        'kontakDomisili.nomorSkDomisili' => 'DOM-2026-99',
        'kontakDomisili.isAnakGuru' => true,
    ]);

    $wizard->assertSee('PT Semen Gresik')
        ->assertSee('SK-MUTASI-001')
        ->assertSee('DOM-2026-99')
        ->assertSee('Ya')
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasNoErrors();

    assertDatabaseHas('registered_siswas', [
        'nisn' => '9988776655',
        'status_domisili' => StatusDomisili::SesuaiMutasiOrtuWali->value,
        'nama_instansi_mutasi' => 'PT Semen Gresik',
        'nomor_surat_penugasan' => 'SK-MUTASI-001',
        'nomor_sk_domisili' => 'DOM-2026-99',
        'is_anak_guru' => 1,
    ]);

    $saved = RegisteredSiswa::where('nisn', '9988776655')->first();
    expect($saved->tgl_surat_penugasan->format('Y-m-d'))->toBe('2026-02-15');
});

it('renders and persists conditional domisili details for SesuaiDomisiliPondok', function () {
    Storage::fake('public');

    $wizard = step5Wizard([
        'biodata.nisn' => '9988776656',
        'kontakDomisili.statusDomisili' => StatusDomisili::SesuaiDomisiliPondok->value,
        'kontakDomisili.namaPondokPesantren' => 'Pesantren Raudlatul Thalibin',
        'kontakDomisili.nomorIzinPondokEmis' => 'NSPP-510033',
        'kontakDomisili.jenjangPondok' => 'PDF',
    ]);

    $wizard->assertSee('Pesantren Raudlatul Thalibin')
        ->assertSee('NSPP-510033')
        ->assertSee('PDF')
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasNoErrors();

    assertDatabaseHas('registered_siswas', [
        'nisn' => '9988776656',
        'status_domisili' => StatusDomisili::SesuaiDomisiliPondok->value,
        'nama_pondok_pesantren' => 'Pesantren Raudlatul Thalibin',
        'nomor_izin_pondok_emis' => 'NSPP-510033',
        'jenjang_pondok' => 'PDF',
    ]);
});

it('renders and persists conditional domisili details for SesuaiDomisiliPantiAsuhan', function () {
    Storage::fake('public');

    $wizard = step5Wizard([
        'biodata.nisn' => '9988776657',
        'kontakDomisili.statusDomisili' => StatusDomisili::SesuaiDomisiliPantiAsuhan->value,
        'kontakDomisili.namaPantiAsuhan' => 'Panti Asuhan Kasih Ibu',
        'kontakDomisili.nomorRegistrasiDinsos' => 'REG-DINSOS-77',
        'kontakDomisili.prioritasPanti' => 'Prioritas 1 (P1)',
    ]);

    $wizard->assertSee('Panti Asuhan Kasih Ibu')
        ->assertSee('REG-DINSOS-77')
        ->assertSee('Prioritas 1 (P1)')
        ->set('pernyataanSetuju', true)
        ->call('submitPendaftaran')
        ->assertHasNoErrors();

    assertDatabaseHas('registered_siswas', [
        'nisn' => '9988776657',
        'status_domisili' => StatusDomisili::SesuaiDomisiliPantiAsuhan->value,
        'nama_panti_asuhan' => 'Panti Asuhan Kasih Ibu',
        'nomor_registrasi_dinsos' => 'REG-DINSOS-77',
        'prioritas_panti' => 'Prioritas 1 (P1)',
    ]);
});
