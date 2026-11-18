<?php

use App\Enums\UserRole;
use App\Livewire\Siswa;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Carbon::setTestNow('2026-06-16 10:00:00');

    $this->siswaUser = User::factory()->create([
        'name' => 'Bambang Sudarsono',
        'email' => 'bambang@example.test',
        'nisn' => '1234567890',
        'role' => UserRole::Siswa,
        'password' => Hash::make('password123'),
    ]);

    $this->registeredSiswa = RegisteredSiswa::factory()->create([
        'user_id' => $this->siswaUser->id,
        'nisn' => '1234567890',
        'nama_lengkap' => 'Bambang Sudarsono',
        'is_activated' => true,
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-12345678',
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('shows student login form for unauthenticated users', function () {
    get('/siswa')
        ->assertOk()
        ->assertSee('Login Calon Siswa')
        ->assertSee('NISN')
        ->assertSee('Password')
        ->assertSee('Lupa Password Akun?')
        ->assertSee('Operator Sekolah yang memverifikasi berkas pendaftaran Anda');
});

it('validates required fields on student login', function () {
    Livewire::test(Siswa::class)
        ->set('nisn', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['nisn' => 'required', 'password' => 'required']);
});

it('fails login with invalid password', function () {
    Livewire::test(Siswa::class)
        ->set('nisn', '1234567890')
        ->set('password', 'wrong-pass')
        ->call('login')
        ->assertHasErrors(['nisn'])
        ->assertSee('NISN atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('fails login with non-existent nisn', function () {
    Livewire::test(Siswa::class)
        ->set('nisn', '9999999999')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['nisn'])
        ->assertSee('NISN atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('fails login if user role is not siswa', function () {
    $operator = User::factory()->create([
        'nip' => '199001012015011001',
        'nisn' => '5555555555',
        'role' => UserRole::Operator,
        'password' => Hash::make('password123'),
    ]);

    Livewire::test(Siswa::class)
        ->set('nisn', '5555555555')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['nisn']);

    expect(Auth::check())->toBeFalse();
});

it('successfully logs in student and displays the dashboard', function () {
    Livewire::test(Siswa::class)
        ->set('nisn', '1234567890')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertSee('Selamat Datang, Bambang Sudarsono')
        ->assertSee('terverifikasi')
        ->assertSee('Logout');

    expect(Auth::check())->toBeTrue()
        ->and(Auth::user()->role)->toBe(UserRole::Siswa);
});

it('shows dashboard directly when already authenticated as siswa', function () {
    actingAs($this->siswaUser);

    get('/siswa')
        ->assertOk()
        ->assertSee('Selamat Datang, Bambang Sudarsono')
        ->assertSee('terverifikasi')
        ->assertSee('Logout');
});

it('allows student to logout from dashboard', function () {
    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('logout')
        ->assertSee('Login Calon Siswa');

    expect(Auth::check())->toBeFalse();
});

it('supports remember me when logging in as siswa', function () {
    Livewire::test(Siswa::class)
        ->set('nisn', '1234567890')
        ->set('password', 'password123')
        ->set('remember', true)
        ->call('login')
        ->assertHasNoErrors();

    expect(Auth::check())->toBeTrue();
});

it('allows student to open and close school choice form', function () {
    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->assertSet('isChoosingSchool', true)
        ->assertSee('Pemilihan Sekolah Tujuan')
        ->call('tutupFormPilihanSekolah')
        ->assertSet('isChoosingSchool', false)
        ->assertDontSee('Pemilihan Sekolah Tujuan');
});

it('validates required school choices', function () {
    actingAs($this->siswaUser);

    $sekolah = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', null)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_sekolah_1_id' => 'required'])
        ->set('pilihan_sekolah_1_id', $sekolah->id)
        ->set('pilihan_sekolah_2_id', $sekolah->id)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_sekolah_2_id' => 'different']);
});

it('successfully saves school choice and updates status seleksi', function () {
    actingAs($this->siswaUser);

    $sekolah1 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);
    $sekolah2 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 2 Surakarta']);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $sekolah1->id)
        ->set('pilihan_sekolah_2_id', $sekolah2->id)
        ->call('simpanPilihanSekolah')
        ->assertHasNoErrors()
        ->assertSet('isChoosingSchool', false)
        ->assertSee('SMA Negeri 1 Surakarta')
        ->assertSee('SMA Negeri 2 Surakarta')
        ->assertSee('Data Pilihan Sekolah');

    $this->registeredSiswa->refresh();
    expect($this->registeredSiswa->pilihan_sekolah_1_id)->toBe($sekolah1->id)
        ->and($this->registeredSiswa->pilihan_sekolah_2_id)->toBe($sekolah2->id)
        ->and($this->registeredSiswa->status_seleksi)->toBe('menunggu')
        ->and($this->registeredSiswa->tgl_pilih_sekolah)->not->toBeNull();
});

it('displays accepted banner with reregistration pending when student is accepted', function () {
    actingAs($this->siswaUser);

    $sekolah = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);
    $this->registeredSiswa->update([
        'pilihan_sekolah_1_id' => $sekolah->id,
        'diterima_sekolah_id' => $sekolah->id,
        'status_seleksi' => 'diterima_pilihan_1',
        'is_daftar_ulang' => false,
    ]);

    Livewire::test(Siswa::class)
        ->assertSee('SELAMAT! Anda Dinyatakan DITERIMA')
        ->assertSee('SMA Negeri 1 Surakarta')
        ->assertSee('Pilihan 1')
        ->assertSee('Belum Hadir / Belum Konfirmasi')
        ->assertDontSee('Ubah Pilihan');
});

it('allows student to download registration proof pdf from dashboard', function () {
    actingAs($this->siswaUser);

    $this->registeredSiswa->update([
        'nomor_pendaftaran' => 'REG-2026-DOWNLOAD',
    ]);

    Livewire::test(Siswa::class)
        ->assertSee('Cetak Bukti Pendaftaran')
        ->call('cetakBuktiPendaftaran')
        ->assertFileDownloaded('bukti-pendaftaran-REG-2026-DOWNLOAD.pdf');
});

it('displays confirmed reregistration badge when student has completed reregistration', function () {
    $sekolah = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);
    $this->registeredSiswa->update([
        'pilihan_sekolah_1_id' => $sekolah->id,
        'diterima_sekolah_id' => $sekolah->id,
        'status_seleksi' => 'diterima_pilihan_1',
        'is_daftar_ulang' => true,
    ]);

    actingAs($this->siswaUser->fresh());

    Livewire::test(Siswa::class)
        ->assertSee('Sudah Daftar Ulang')
        ->assertSee('Dikonfirmasi oleh Operator');
});

it('displays rejection banner when student is not admitted', function () {
    actingAs($this->siswaUser);

    $sekolah = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);
    $this->registeredSiswa->update([
        'pilihan_sekolah_1_id' => $sekolah->id,
        'status_seleksi' => 'tidak_lolos',
    ]);

    Livewire::test(Siswa::class)
        ->assertSee('Mohon Maaf, Anda Belum Lolos Seleksi')
        ->assertDontSee('Ubah Pilihan');
});

it('prevents modifying school choice if selection has been finalized', function () {
    actingAs($this->siswaUser);

    $sekolah = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);
    $this->registeredSiswa->update([
        'pilihan_sekolah_1_id' => $sekolah->id,
        'status_seleksi' => 'diterima_pilihan_1',
    ]);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->assertSet('isChoosingSchool', false)
        ->assertSee('Pilihan sekolah tidak dapat diubah');
});

it('displays warning on dashboard when student is displaced in both choices', function () {
    $sekolah1 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta', 'kuota' => 1]);
    $sekolah2 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 2 Surakarta', 'kuota' => 1]);

    // Pendaftar lain dengan nilai lebih tinggi di kedua sekolah
    RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 98.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
    ]);
    RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 97.0,
        'pilihan_sekolah_1_id' => $sekolah2->id,
    ]);

    // Siswa yang sedang login dengan nilai lebih rendah
    $this->registeredSiswa->update([
        'rata_rata_nilai_rapor' => 75.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'status_seleksi' => 'menunggu',
    ]);

    actingAs($this->siswaUser->fresh());

    Livewire::test(Siswa::class)
        ->assertSee('Perhatian: Nilai Anda Tergeser dari Kuota')
        ->assertSee('SMA Negeri 1 Surakarta')
        ->assertSee('SMA Negeri 2 Surakarta')
        ->assertSee('Ubah Pilihan Sekolah')
        ->assertSee('Tergeser dari Kuota');
});

it('displays allocation notice on dashboard when student is displaced in choice 1 but admitted in choice 2', function () {
    $sekolah1 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta', 'kuota' => 1]);
    $sekolah2 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 2 Surakarta', 'kuota' => 1]);

    // Sekolah 1 kuota penuh oleh siswa lain
    RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 99.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
    ]);

    // Siswa yang sedang login tergeser di Sekolah 1 tapi masih masuk kuota di Sekolah 2
    $this->registeredSiswa->update([
        'rata_rata_nilai_rapor' => 88.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'status_seleksi' => 'menunggu',
    ]);

    actingAs($this->siswaUser->fresh());

    Livewire::test(Siswa::class)
        ->assertSee('Info Alokasi')
        ->assertSee('Masuk Kuota di Pilihan 2')
        ->assertSee('SMA Negeri 2 Surakarta');
});

it('requires selecting a jurusan when student chooses an SMK for school choice 1', function () {
    $smk = Sekolah::factory()->smk(['Teknik Komputer dan Jaringan', 'Rekayasa Perangkat Lunak'])->create();

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $smk->id)
        ->set('pilihan_jurusan_1_id', null)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_jurusan_1_id' => 'required']);
});

it('requires selecting a jurusan when student chooses an SMK for school choice 2', function () {
    $smk1 = Sekolah::factory()->smk(['Teknik Mesin', 'Teknik Otomotif'])->create(['nama_sekolah' => 'SMK Negeri 1 Surakarta']);
    $smk2 = Sekolah::factory()->smk(['Rekayasa Perangkat Lunak', 'Teknik Komputer'])->create(['nama_sekolah' => 'SMK Negeri 2 Surakarta']);
    $jurusanSmk1 = $smk1->jurusans()->first();

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $smk1->id)
        ->set('pilihan_jurusan_1_id', $jurusanSmk1->id)
        ->set('pilihan_sekolah_2_id', $smk2->id)
        ->set('pilihan_jurusan_2_id', null)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_jurusan_2_id' => 'required']);
});

it('rejects jurusan if it belongs to a different school', function () {
    $smk1 = Sekolah::factory()->smk(['RPL', 'TKJ'])->create();
    $smk2 = Sekolah::factory()->smk(['Multimedia', 'Animasi'])->create();

    $jurusanSmk2 = $smk2->jurusans()->first();

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $smk1->id)
        ->set('pilihan_jurusan_1_id', $jurusanSmk2->id)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_jurusan_1_id']);
});

it('fails validation when choice 1 is SMA and choice 2 is SMK', function () {
    $sma = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta', 'jenis_sekolah' => 'SMA']);
    $smk = Sekolah::factory()->smk(['RPL', 'TKJ'])->create(['nama_sekolah' => 'SMK Negeri 2 Surakarta']);
    $jurusan = $smk->jurusans()->first();

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $sma->id)
        ->set('pilihan_sekolah_2_id', $smk->id)
        ->set('pilihan_jurusan_2_id', $jurusan->id)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_sekolah_2_id'])
        ->assertSee('Sekolah Pilihan 2 harus berjenis sama dengan Pilihan 1');
});

it('fails validation when choice 1 is SMK and choice 2 is SMA', function () {
    $smk = Sekolah::factory()->smk(['RPL', 'TKJ'])->create(['nama_sekolah' => 'SMK Negeri 2 Surakarta']);
    $jurusan = $smk->jurusans()->first();
    $sma = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta', 'jenis_sekolah' => 'SMA']);

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $smk->id)
        ->set('pilihan_jurusan_1_id', $jurusan->id)
        ->set('pilihan_sekolah_2_id', $sma->id)
        ->call('simpanPilihanSekolah')
        ->assertHasErrors(['pilihan_sekolah_2_id'])
        ->assertSee('Sekolah Pilihan 2 harus berjenis sama dengan Pilihan 1');
});

it('automatically clears choice 2 if choice 1 changes to a different school type', function () {
    $sma1 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta', 'jenis_sekolah' => 'SMA']);
    $sma2 = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 2 Surakarta', 'jenis_sekolah' => 'SMA']);
    $smk = Sekolah::factory()->smk(['RPL', 'TKJ'])->create(['nama_sekolah' => 'SMK Negeri 2 Surakarta']);

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $sma1->id)
        ->set('pilihan_sekolah_2_id', $sma2->id)
        ->set('pilihan_sekolah_1_id', $smk->id)
        ->assertSet('pilihan_sekolah_2_id', null)
        ->assertSet('pilihan_jurusan_2_id', null);
});

it('successfully saves choices with SMK and jurusans and displays them on dashboard', function () {
    $smk1 = Sekolah::factory()->smk(['Rekayasa Perangkat Lunak', 'Teknik Komputer dan Jaringan'])->create([
        'nama_sekolah' => 'SMK Negeri 2 Surakarta',
    ]);
    $jurusanRpl = $smk1->jurusans()->where('nama_jurusan', 'Rekayasa Perangkat Lunak')->first();

    $smk2 = Sekolah::factory()->smk(['Multimedia', 'Animasi'])->create([
        'nama_sekolah' => 'SMK Negeri 9 Surakarta',
    ]);
    $jurusanAnimasi = $smk2->jurusans()->where('nama_jurusan', 'Animasi')->first();

    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->call('bukaFormPilihanSekolah')
        ->set('pilihan_sekolah_1_id', $smk1->id)
        ->set('pilihan_jurusan_1_id', $jurusanRpl->id)
        ->set('pilihan_sekolah_2_id', $smk2->id)
        ->set('pilihan_jurusan_2_id', $jurusanAnimasi->id)
        ->call('simpanPilihanSekolah')
        ->assertHasNoErrors()
        ->assertSet('isChoosingSchool', false)
        ->assertSee('SMK Negeri 2 Surakarta')
        ->assertSee('Jurusan: Rekayasa Perangkat Lunak')
        ->assertSee('SMK Negeri 9 Surakarta')
        ->assertSee('Jurusan: Animasi');

    expect($this->registeredSiswa->fresh()->pilihan_sekolah_1_id)->toBe($smk1->id)
        ->and($this->registeredSiswa->fresh()->pilihan_jurusan_1_id)->toBe($jurusanRpl->id)
        ->and($this->registeredSiswa->fresh()->pilihan_sekolah_2_id)->toBe($smk2->id)
        ->and($this->registeredSiswa->fresh()->pilihan_jurusan_2_id)->toBe($jurusanAnimasi->id);
});

it('prevents student from opening and saving school choice before 15 June 2026', function () {
    Carbon::setTestNow('2026-06-10 10:00:00');
    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->assertSee('Belum Dibuka (Mulai 15 Juni 2026)')
        ->assertSee('Tahap Pendaftaran Sekolah Belum Dibuka')
        ->assertSee('Dibuka 15 Juni 2026')
        ->call('bukaFormPilihanSekolah')
        ->assertSet('isChoosingSchool', false)
        ->assertSee('Pendaftaran dan pemilihan sekolah belum dapat dilakukan sebelum jadwal resminya (15 Juni 2026).')
        ->call('simpanPilihanSekolah')
        ->assertSee('Pendaftaran dan pemilihan sekolah belum dapat dilakukan sebelum jadwal resminya (15 Juni 2026).');
});

it('allows student to open and save school choice starting 15 June 2026', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    actingAs($this->siswaUser);

    $sekolah = Sekolah::factory()->create(['nama_sekolah' => 'SMA Negeri 1 Surakarta']);

    Livewire::test(Siswa::class)
        ->assertSee('Jadwal Berlangsung')
        ->assertSee('Pendaftaran Sekolah Dibuka!')
        ->assertSee('Pilih Sekolah Tujuan Sekarang')
        ->call('bukaFormPilihanSekolah')
        ->assertSet('isChoosingSchool', true)
        ->set('pilihan_sekolah_1_id', $sekolah->id)
        ->call('simpanPilihanSekolah')
        ->assertHasNoErrors()
        ->assertSet('isChoosingSchool', false);

    expect($this->registeredSiswa->fresh()->pilihan_sekolah_1_id)->toBe($sekolah->id);
});

it('prevents student from opening and saving school choice starting 19 June 2026 after registration closes', function () {
    Carbon::setTestNow('2026-06-19 10:00:00');
    actingAs($this->siswaUser);

    Livewire::test(Siswa::class)
        ->assertSee('Telah Ditutup (Selesai 18 Juni 2026)')
        ->assertSee('Tahap Pendaftaran Sekolah Telah Ditutup')
        ->assertSee('Pendaftaran Ditutup')
        ->call('bukaFormPilihanSekolah')
        ->assertSet('isChoosingSchool', false)
        ->assertSee('Pendaftaran dan pemilihan sekolah tujuan telah ditutup pada tanggal 18 Juni 2026 pukul 23:59 WIB.')
        ->call('simpanPilihanSekolah')
        ->assertSee('Pendaftaran dan pemilihan sekolah tujuan telah ditutup pada tanggal 18 Juni 2026 pukul 23:59 WIB.');
});
