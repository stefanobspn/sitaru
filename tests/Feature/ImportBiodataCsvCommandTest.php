<?php

use App\Models\BiodataSiswa;
use Illuminate\Support\Facades\File;

it('fails gracefully when csv file does not exist', function () {
    $this->artisan('app:import-biodata-csv', ['file' => '/path/to/non_existent.csv'])
        ->expectsOutputToContain('File CSV tidak ditemukan')
        ->assertFailed();
});

it('imports biodata siswa from csv correctly', function () {
    $tempCsv = tempnam(sys_get_temp_dir(), 'test_biodata_').'.csv';
    $csvContent = implode("\n", [
        'nisn,nik,nama_lengkap,nama_sekolah_asal,tempat_lahir,tanggal_lahir,alamat,provinsi,kabupaten_kota,kecamatan,kelurahan,rt,rw',
        '0091122334,3372010101090001,Siswa Uji Coba,SMP N 1 Solo,Surakarta,2009-01-01,Jl. Solo,Jawa Tengah,Kota Surakarta,Laweyan,Kerten,01,02',
        '0091122335,3372010101090002,Siswa Uji Kosong,,Boyolali,2009-02-02,Jl. Boyolali,Jawa Tengah,Kabupaten Boyolali,Banyudono,Ketaon,,',
    ]);
    File::put($tempCsv, $csvContent);

    $this->artisan('app:import-biodata-csv', ['file' => $tempCsv])
        ->expectsOutputToContain('Proses impor data biodata siswa selesai dengan sukses!')
        ->assertSuccessful();

    $siswa1 = BiodataSiswa::where('nisn', '0091122334')->first();
    expect($siswa1)->not->toBeNull()
        ->and($siswa1->nama_lengkap)->toBe('Siswa Uji Coba')
        ->and($siswa1->nama_sekolah_asal)->toBe('SMP N 1 Solo')
        ->and($siswa1->rt)->toBe('01');

    // Siswa dengan field kosong di fallback ke '-'
    $siswa2 = BiodataSiswa::where('nisn', '0091122335')->first();
    expect($siswa2)->not->toBeNull()
        ->and($siswa2->nama_lengkap)->toBe('Siswa Uji Kosong')
        ->and($siswa2->nama_sekolah_asal)->toBe('-')
        ->and($siswa2->rt)->toBe('-')
        ->and($siswa2->rw)->toBe('-');

    // Running again updates the record without creating duplicate rows
    $this->artisan('app:import-biodata-csv', ['file' => $tempCsv])
        ->expectsOutputToContain('Data Diperbarui')
        ->assertSuccessful();

    expect(BiodataSiswa::where('nisn', '0091122334')->count())->toBe(1);

    File::delete($tempCsv);
});
