<?php

use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Services\SeleksiService;

it('places student into choice 2 when choice 1 quota is full, and marks tergeser if choice 2 also full', function () {
    $sekolahA = Sekolah::factory()->create(['nama_sekolah' => 'Sekolah A', 'kuota' => 1]);
    $sekolahB = Sekolah::factory()->create(['nama_sekolah' => 'Sekolah B', 'kuota' => 1]);

    // Siswa 1 (100) -> P1: A (masuk), P2: B
    $siswa1 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa 1',
        'rata_rata_nilai_rapor' => 100.0,
        'pilihan_sekolah_1_id' => $sekolahA->id,
        'pilihan_sekolah_2_id' => $sekolahB->id,
    ]);

    // Siswa 2 (90) -> P1: A (tergeser karena kuota A cuma 1 dan diambil Siswa 1), P2: B (otomatis ditempatkan di B)
    $siswa2 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa 2',
        'rata_rata_nilai_rapor' => 90.0,
        'pilihan_sekolah_1_id' => $sekolahA->id,
        'pilihan_sekolah_2_id' => $sekolahB->id,
    ]);

    // Siswa 3 (80) -> P1: B (tergeser karena kuota B cuma 1 dan diambil Siswa 2), P2: A (A juga penuh) -> tergeser di kedua pilihan!
    $siswa3 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa 3',
        'rata_rata_nilai_rapor' => 80.0,
        'pilihan_sekolah_1_id' => $sekolahB->id,
        'pilihan_sekolah_2_id' => $sekolahA->id,
    ]);

    $hasil = SeleksiService::hitungPenempatan();

    // Sekolah A: hanya diisi Siswa 1 (Pilihan 1)
    $lolosA = $hasil['admitted_by_school'][$sekolahA->id];
    expect($lolosA)->toHaveCount(1)
        ->and($lolosA->first()->id)->toBe($siswa1->id)
        ->and($lolosA->first()->penempatan_pilihan)->toBe(1);

    // Sekolah B: otomatis diisi Siswa 2 (Pilihan 2)
    $lolosB = $hasil['admitted_by_school'][$sekolahB->id];
    expect($lolosB)->toHaveCount(1)
        ->and($lolosB->first()->id)->toBe($siswa2->id)
        ->and($lolosB->first()->penempatan_pilihan)->toBe(2);

    // Status Siswa 1: Lolos Pilihan 1
    expect($hasil['status_by_siswa'][$siswa1->id]['status'])->toBe('lolos_pilihan_1')
        ->and($hasil['status_by_siswa'][$siswa1->id]['diterima_sekolah_id'])->toBe($sekolahA->id)
        ->and($hasil['status_by_siswa'][$siswa1->id]['is_tergeser_semua'])->toBeFalse();

    // Status Siswa 2: Tergeser Pilihan 1, Otomatis Lolos Pilihan 2
    expect($hasil['status_by_siswa'][$siswa2->id]['status'])->toBe('lolos_pilihan_2')
        ->and($hasil['status_by_siswa'][$siswa2->id]['diterima_sekolah_id'])->toBe($sekolahB->id)
        ->and($hasil['status_by_siswa'][$siswa2->id]['is_tergeser_pilihan_1'])->toBeTrue()
        ->and($hasil['status_by_siswa'][$siswa2->id]['is_tergeser_semua'])->toBeFalse();

    // Status Siswa 3: Tergeser di Kedua Pilihan
    expect($hasil['status_by_siswa'][$siswa3->id]['status'])->toBe('tergeser_semua')
        ->and($hasil['status_by_siswa'][$siswa3->id]['diterima_sekolah_id'])->toBeNull()
        ->and($hasil['status_by_siswa'][$siswa3->id]['is_tergeser_semua'])->toBeTrue();

    // Test Finalisasi ke Database
    SeleksiService::finalisasi();

    expect($siswa1->fresh()->status_seleksi)->toBe('diterima_pilihan_1')
        ->and($siswa1->fresh()->diterima_sekolah_id)->toBe($sekolahA->id)
        ->and($siswa2->fresh()->status_seleksi)->toBe('diterima_pilihan_2')
        ->and($siswa2->fresh()->diterima_sekolah_id)->toBe($sekolahB->id)
        ->and($siswa3->fresh()->status_seleksi)->toBe('tidak_lolos')
        ->and($siswa3->fresh()->diterima_sekolah_id)->toBeNull();
});

it('ranks applicants primarily by nilai_akhir descending', function () {
    $sekolah = Sekolah::factory()->create(['kuota' => 1]);

    // Siswa X: NRR 80, Nilai Akhir 92.5
    $siswaX = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa X',
        'rata_rata_nilai_rapor' => 80.0,
        'nilai_akhir' => 92.5,
        'pilihan_sekolah_1_id' => $sekolah->id,
    ]);

    // Siswa Y: NRR 90, Nilai Akhir 85.0
    $siswaY = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa Y',
        'rata_rata_nilai_rapor' => 90.0,
        'nilai_akhir' => 85.0,
        'pilihan_sekolah_1_id' => $sekolah->id,
    ]);

    $hasil = SeleksiService::hitungPenempatan();

    $admitted = $hasil['admitted_by_school'][$sekolah->id];
    expect($admitted)->toHaveCount(1)
        ->and($admitted->first()->id)->toBe($siswaX->id);
});
