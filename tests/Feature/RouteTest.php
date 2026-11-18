<?php

use App\Models\RegisteredSiswa;
use App\Models\Sekolah;

use function Pest\Laravel\get;

it('renders the landing page successfully with dynamic stats, header, and footer', function () {
    Sekolah::factory()->create(['kuota' => 150]);
    RegisteredSiswa::factory()->create();

    get(route('index'))
        ->assertOk()
        ->assertViewIs('index')
        ->assertSee('SITARU')
        ->assertSee('Jurnal Seleksi')
        ->assertSee('Login Siswa')
        ->assertSee('Ajukan Akun')
        ->assertSee('Live Statistik')
        ->assertSee('150')
        ->assertSee('Total Kuota Tersedia')
        ->assertSee('Dinas Pendidikan dan Kebudayaan Provinsi Jawa Tengah');
});

it('renders cetak bukti pdf when nomor pendaftaran is found', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nomor_pendaftaran' => 'REG-2026-TEST88',
        'nama_lengkap' => 'Alifa Nur',
    ]);

    $response = get(route('pendaftaran.cetak-bukti', ['nomor' => 'REG-2026-TEST88']));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect($response->getContent())->toContain('%PDF');
});

it('returns 404 when nomor pendaftaran is not found', function () {
    get(route('pendaftaran.cetak-bukti', ['nomor' => 'REG-NONEXISTENT']))
        ->assertNotFound();
});
