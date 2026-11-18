<?php

use App\Models\BiodataSiswa;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\CarbonInterface;

it('creates a registered siswa using factory', function () {
    $siswa = RegisteredSiswa::factory()->create();

    expect($siswa->exists)->toBeTrue()
        ->and($siswa->nomor_pendaftaran)->toStartWith('REG-');
});

it('casts attributes to their correct types', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nilai_rapor' => ['matematika' => [1 => 90]],
        'is_activated' => 1,
        'latitude' => '-7.55612300',
        'longitude' => '110.82345600',
        'rata_rata_nilai_rapor' => '85.50',
        'nrtka' => '80.00',
        'nilai_akhir' => '82.75',
        'tgl_cetak_kk' => '2023-05-10',
        'tgl_surat_penugasan' => '2023-06-15',
        'is_anak_guru' => 1,
    ]);

    expect($siswa->nilai_rapor)->toBeArray()
        ->and($siswa->nilai_rapor['matematika'][1])->toBe(90)
        ->and($siswa->is_activated)->toBeTrue()
        ->and($siswa->latitude)->toBeFloat()->toEqual(-7.556123)
        ->and($siswa->longitude)->toBeFloat()->toEqual(110.823456)
        ->and($siswa->rata_rata_nilai_rapor)->toBeFloat()->toEqual(85.50)
        ->and($siswa->nrtka)->toBeFloat()->toEqual(80.00)
        ->and($siswa->nilai_akhir)->toBeFloat()->toEqual(82.75)
        ->and($siswa->tgl_cetak_kk)->toBeInstanceOf(CarbonInterface::class)
        ->and($siswa->tgl_cetak_kk->format('Y-m-d'))->toBe('2023-05-10')
        ->and($siswa->tgl_surat_penugasan)->toBeInstanceOf(CarbonInterface::class)
        ->and($siswa->tgl_surat_penugasan->format('Y-m-d'))->toBe('2023-06-15')
        ->and($siswa->is_anak_guru)->toBeTrue();
});

it('calculates nilai akhir correctly on model', function () {
    $siswa = RegisteredSiswa::factory()->make([
        'rata_rata_nilai_rapor' => 80.0,
        'nrtka' => 70.0,
        'nilai_kejuaraan' => '2.50',
        'nilai_organisasi' => 0.75,
    ]);

    expect($siswa->hitungNilaiAkhir())->toBe(78.25);
});

it('belongs to a biodata siswa', function () {
    $biodata = BiodataSiswa::factory()->create();
    $siswa = RegisteredSiswa::factory()->create([
        'biodata_siswa_id' => $biodata->id,
    ]);

    expect($siswa->biodataSiswa)->toBeInstanceOf(BiodataSiswa::class)
        ->and($siswa->biodataSiswa->id)->toBe($biodata->id);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $siswa = RegisteredSiswa::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($siswa->user)->toBeInstanceOf(User::class)
        ->and($siswa->user->id)->toBe($user->id)
        ->and($user->registeredSiswa->id)->toBe($siswa->id);
});

it('belongs to school choices and accepted school', function () {
    $sekolah1 = Sekolah::factory()->create(['nama_sekolah' => 'SMA 1']);
    $sekolah2 = Sekolah::factory()->create(['nama_sekolah' => 'SMA 2']);

    $siswa = RegisteredSiswa::factory()->create([
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'diterima_sekolah_id' => $sekolah1->id,
    ]);

    expect($siswa->pilihanSekolah1->id)->toBe($sekolah1->id)
        ->and($siswa->pilihanSekolah2->id)->toBe($sekolah2->id)
        ->and($siswa->diterimaSekolah->id)->toBe($sekolah1->id);
});
