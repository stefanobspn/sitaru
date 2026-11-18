<?php

use App\Livewire\Seleksi;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use Livewire\Livewire;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->sekolah = Sekolah::factory()->create([
        'nama_sekolah' => 'SMA Negeri 1 Surakarta',
        'kuota' => 2,
    ]);

    $this->siswa1 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Andi Wijaya',
        'nisn' => '1111111111',
        'rata_rata_nilai_rapor' => 82.50,
        'pilihan_sekolah_1_id' => $this->sekolah->id,
    ]);

    $this->siswa2 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Citra Lestari',
        'nisn' => '2222222222',
        'rata_rata_nilai_rapor' => 91.00,
        'pilihan_sekolah_1_id' => $this->sekolah->id,
    ]);

    $this->siswa3 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Budi Santoso',
        'nisn' => '3333333333',
        'rata_rata_nilai_rapor' => 88.00,
        'pilihan_sekolah_1_id' => $this->sekolah->id,
    ]);
});

it('renders the selection journal page successfully', function () {
    get('/seleksi')
        ->assertOk()
        ->assertSee('Jurnal Hasil Seleksi SPMB')
        ->assertSee('SMA Negeri 1 Surakarta');
});

it('ranks applicants purely based on rata_rata_nilai_rapor descending and only displays students within quota', function () {
    // Siswa 2 (91.00) => Rank 1 (Masuk Kuota)
    // Siswa 3 (88.00) => Rank 2 (Masuk Kuota)
    // Siswa 1 (82.50) => Rank 3 (Tergeser, tidak ditampilkan di tabel karena kuota = 2)

    Livewire::test(Seleksi::class, ['selectedSekolahId' => $this->sekolah->id])
        ->assertSeeInOrder([
            'Citra Lestari',
            'Budi Santoso',
        ])
        ->assertDontSee('Andi Wijaya')
        ->assertDontSee('Tergeser');
});

it('filters journal list when searching by name, nisn, or registration number', function () {
    Livewire::test(Seleksi::class, ['selectedSekolahId' => $this->sekolah->id])
        ->set('search', 'Citra')
        ->assertSee('Citra Lestari')
        ->assertDontSee('Budi Santoso')
        ->assertDontSee('Andi Wijaya')
        ->set('search', '3333333333')
        ->assertSee('Budi Santoso')
        ->assertDontSee('Citra Lestari')
        ->set('search', '1111111111')
        ->assertDontSee('Andi Wijaya');
});

it('handles switching between different schools', function () {
    $sekolah2 = Sekolah::factory()->create([
        'nama_sekolah' => 'SMK Negeri 2 Surakarta',
        'kuota' => 5,
    ]);

    $siswaSekolah2 = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Doni Saputra',
        'rata_rata_nilai_rapor' => 95.00,
        'pilihan_sekolah_1_id' => $sekolah2->id,
    ]);

    Livewire::test(Seleksi::class)
        ->set('selectedSekolahId', $sekolah2->id)
        ->assertSee('SMK Negeri 2 Surakarta')
        ->assertSee('Doni Saputra')
        ->assertDontSee('Citra Lestari');
});

it('renders jurusan column and values when viewing an SMK in seleksi', function () {
    $smk = Sekolah::factory()->smk(['Teknik Komputer dan Jaringan', 'Rekayasa Perangkat Lunak'])->create([
        'nama_sekolah' => 'SMK Negeri 2 Surakarta',
        'kuota' => 5,
    ]);
    $jurusanTKJ = $smk->jurusans()->where('nama_jurusan', 'Teknik Komputer dan Jaringan')->first();

    RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Rizky Pratama',
        'rata_rata_nilai_rapor' => 95.00,
        'pilihan_sekolah_1_id' => $smk->id,
        'pilihan_jurusan_1_id' => $jurusanTKJ->id,
    ]);

    // SMA should not show Jurusan column header
    Livewire::test(Seleksi::class, ['selectedSekolahId' => $this->sekolah->id])
        ->assertDontSee('Jurusan');

    // SMK should show Jurusan column header and chosen jurusan
    Livewire::test(Seleksi::class, ['selectedSekolahId' => $smk->id])
        ->assertSee('Jurusan')
        ->assertSee('Teknik Komputer dan Jaringan')
        ->assertSee('Rizky Pratama');
});

it('filters applicants by jurusan when a jurusan is selected for an SMK', function () {
    $smk = Sekolah::factory()->smk(['Teknik Komputer dan Jaringan', 'Rekayasa Perangkat Lunak'])->create([
        'nama_sekolah' => 'SMK Negeri 2 Surakarta',
        'kuota' => 5,
    ]);
    $jurusanTKJ = $smk->jurusans()->where('nama_jurusan', 'Teknik Komputer dan Jaringan')->first();
    $jurusanRPL = $smk->jurusans()->where('nama_jurusan', 'Rekayasa Perangkat Lunak')->first();

    RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Ahmad RPL',
        'rata_rata_nilai_rapor' => 95.00,
        'pilihan_sekolah_1_id' => $smk->id,
        'pilihan_jurusan_1_id' => $jurusanRPL->id,
    ]);

    RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Bagas TKJ',
        'rata_rata_nilai_rapor' => 90.00,
        'pilihan_sekolah_1_id' => $smk->id,
        'pilihan_jurusan_1_id' => $jurusanTKJ->id,
    ]);

    // Tanpa filter jurusan: tampilkan keduanya dan label Filter Jurusan
    Livewire::test(Seleksi::class, ['selectedSekolahId' => $smk->id])
        ->assertSee('Filter Jurusan')
        ->assertSee('Ahmad RPL')
        ->assertSee('Bagas TKJ');

    // Filter RPL: hanya Ahmad yang tampil
    Livewire::test(Seleksi::class, ['selectedSekolahId' => $smk->id])
        ->set('selectedJurusanId', $jurusanRPL->id)
        ->assertSee('Ahmad RPL')
        ->assertDontSee('Bagas TKJ');

    // Filter TKJ: hanya Bagas yang tampil
    Livewire::test(Seleksi::class, ['selectedSekolahId' => $smk->id])
        ->set('selectedJurusanId', $jurusanTKJ->id)
        ->assertSee('Bagas TKJ')
        ->assertDontSee('Ahmad RPL');
});

it('resets selectedJurusanId when switching to a different school and hides filter on SMA', function () {
    $smk = Sekolah::factory()->smk(['Rekayasa Perangkat Lunak', 'Teknik Komputer'])->create([
        'nama_sekolah' => 'SMK Negeri 2 Surakarta',
    ]);
    $jurusanRPL = $smk->jurusans()->first();

    Livewire::test(Seleksi::class, ['selectedSekolahId' => $smk->id])
        ->set('selectedJurusanId', $jurusanRPL->id)
        ->set('selectedSekolahId', $this->sekolah->id)
        ->assertSet('selectedJurusanId', null)
        ->assertDontSee('Filter Jurusan');
});
