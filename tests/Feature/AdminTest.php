<?php

use App\Enums\UserRole;
use App\Livewire\Admin;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = User::query()->updateOrCreate(
        ['email' => 'admin@sitaru.test'],
        [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]
    );

    $this->operator = User::query()->updateOrCreate(
        ['nip' => '198501012010011001'],
        [
            'name' => 'Operator Sekolah',
            'email' => 'operator@sitaru.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Operator,
        ]
    );
});

it('shows the admin login form for unauthenticated users', function () {
    get('/admin')
        ->assertOk()
        ->assertSee('Login Admin')
        ->assertSee('Email')
        ->assertSee('Password');
});

it('validates required fields on admin login', function () {
    Livewire::test(Admin::class)
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email' => 'required', 'password' => 'required']);
});

it('fails login with invalid password', function () {
    Livewire::test(Admin::class)
        ->set('email', 'admin@sitaru.test')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertSee('Email atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('prevents operator user from logging in via admin login', function () {
    Livewire::test(Admin::class)
        ->set('email', 'operator@sitaru.test')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    expect(Auth::check())->toBeFalse();
});

it('successfully logs in admin and displays the dashboard', function () {
    Livewire::test(Admin::class)
        ->set('email', 'admin@sitaru.test')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertSee('Beranda')
        ->assertSee('Logout');

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->role)->toBe(UserRole::Admin);
});

it('shows dashboard directly when already authenticated as admin', function () {
    actingAs($this->admin);

    get('/admin')
        ->assertOk()
        ->assertSee('Beranda')
        ->assertSee('Logout');
});

it('allows admin to logout from the dashboard', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('logout')
        ->assertSee('Login Admin');

    expect(Auth::check())->toBeFalse();
});

it('can switch active menu to manage in admin dashboard', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->assertSet('activeMenu', 'manage')
        ->assertSee('Manajemen SPMB');
});

it('can toggle create school form in manage menu', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->assertSet('isCreatingSchool', true)
        ->assertSee('Tambah Sekolah Baru')
        ->assertSee('Kembali')
        ->call('closeCreateSchoolForm')
        ->assertSet('isCreatingSchool', false)
        ->assertSee('Manajemen SPMB');
});

it('supports remember me when logging in as admin', function () {
    Livewire::test(Admin::class)
        ->set('email', 'admin@sitaru.test')
        ->set('password', 'password')
        ->set('remember', true)
        ->call('login')
        ->assertHasNoErrors();

    expect(Auth::check())->toBeTrue();
});

it('validates required fields when creating a school', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('npsn_sekolah', '')
        ->set('nama_sekolah', '')
        ->set('nip_operator', '')
        ->call('createSchool')
        ->assertHasErrors([
            'npsn_sekolah' => 'required',
            'nama_sekolah' => 'required',
            'nip_operator' => 'required',
        ])
        ->assertSet('isCreatingSchool', true);
});

it('successfully creates a school with operator and renders it in the table', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('npsn_sekolah', '20327988')
        ->set('nama_sekolah', 'SMA Negeri 1 Surakarta')
        ->set('nip_operator', '198701012015011002')
        ->set('password_operator', 'secret123')
        ->set('kuota_awal', '120')
        ->call('createSchool')
        ->assertHasNoErrors()
        ->assertSet('isCreatingSchool', false)
        ->assertSee('SMA Negeri 1 Surakarta')
        ->assertSee('20327988')
        ->assertSee('198701012015011002')
        ->assertSee('120');

    assertDatabaseHas('users', [
        'nip' => '198701012015011002',
        'role' => UserRole::Operator,
        'name' => null,
    ]);

    assertDatabaseHas('sekolahs', [
        'npsn' => '20327988',
        'nama_sekolah' => 'SMA Negeri 1 Surakarta',
        'kuota' => 120,
    ]);

    $sekolah = Sekolah::where('npsn', '20327988')->first();
    expect($sekolah)->not->toBeNull()
        ->and($sekolah->operator)->not->toBeNull()
        ->and($sekolah->operator->nip)->toBe('198701012015011002');
});

it('prevents creating a school with duplicate npsn or duplicate nip', function () {
    actingAs($this->admin);

    $existingSekolah = Sekolah::factory()->withOperator()->create([
        'npsn' => '20327988',
    ]);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('npsn_sekolah', '20327988')
        ->set('nama_sekolah', 'SMA Negeri 2 Surakarta')
        ->set('nip_operator', $existingSekolah->operator->nip)
        ->call('createSchool')
        ->assertHasErrors([
            'npsn_sekolah' => 'unique',
            'nip_operator' => 'unique',
        ])
        ->assertSet('isCreatingSchool', true);
});

it('can finalize selection results based on merit ranking and school quota', function () {
    actingAs($this->admin);

    $sekolah1 = Sekolah::factory()->create(['kuota' => 2]);
    $sekolah2 = Sekolah::factory()->create(['kuota' => 1]);

    // Siswa A: nilai 95, pilihan 1 ke Sekolah 1
    $siswaA = RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 95.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'status_seleksi' => 'menunggu',
    ]);

    // Siswa B: nilai 90, pilihan 1 ke Sekolah 1
    $siswaB = RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 90.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'status_seleksi' => 'menunggu',
    ]);

    // Siswa C: nilai 85, pilihan 1 ke Sekolah 1 (tergeser kuota 2), pilihan 2 ke Sekolah 2 (kuota 1)
    $siswaC = RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 85.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'status_seleksi' => 'menunggu',
    ]);

    // Siswa D: nilai 80, pilihan 1 ke Sekolah 1, pilihan 2 ke Sekolah 2 (tidak lolos karena kuota penuh)
    $siswaD = RegisteredSiswa::factory()->create([
        'rata_rata_nilai_rapor' => 80.0,
        'pilihan_sekolah_1_id' => $sekolah1->id,
        'pilihan_sekolah_2_id' => $sekolah2->id,
        'status_seleksi' => 'menunggu',
    ]);

    Livewire::test(Admin::class)
        ->call('finalisasiHasilSeleksi')
        ->assertSee('Hasil seleksi berhasil difinalisasi')
        ->assertSee('Hasil Telah Diumumkan');

    expect($siswaA->fresh()->status_seleksi)->toBe('diterima_pilihan_1')
        ->and($siswaA->fresh()->diterima_sekolah_id)->toBe($sekolah1->id)
        ->and($siswaB->fresh()->status_seleksi)->toBe('diterima_pilihan_1')
        ->and($siswaB->fresh()->diterima_sekolah_id)->toBe($sekolah1->id)
        ->and($siswaC->fresh()->status_seleksi)->toBe('diterima_pilihan_2')
        ->and($siswaC->fresh()->diterima_sekolah_id)->toBe($sekolah2->id)
        ->and($siswaD->fresh()->status_seleksi)->toBe('tidak_lolos')
        ->and($siswaD->fresh()->diterima_sekolah_id)->toBeNull();

    // Test batalkan finalisasi
    Livewire::test(Admin::class)
        ->call('batalkanFinalisasi')
        ->assertSee('Status finalisasi seleksi berhasil dibatalkan');

    expect($siswaA->fresh()->status_seleksi)->toBe('menunggu')
        ->and($siswaA->fresh()->diterima_sekolah_id)->toBeNull()
        ->and($siswaD->fresh()->status_seleksi)->toBe('menunggu');
});

it('can add and remove jurusans dynamically when creating a school', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('jenis_sekolah', 'SMK')
        ->assertCount('jurusans', 2)
        ->call('addJurusan')
        ->assertCount('jurusans', 3)
        ->call('removeJurusan', 1)
        ->assertCount('jurusans', 2);
});

it('validates that creating SMK requires at least 2 jurusans', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('npsn_sekolah', '20327999')
        ->set('nama_sekolah', 'SMK Negeri 1 Test')
        ->set('jenis_sekolah', 'SMK')
        ->set('nip_operator', '199001012020011005')
        ->set('kuota_awal', '100')
        ->set('jurusans', [
            'Rekayasa Perangkat Lunak',
        ])
        ->call('createSchool')
        ->assertHasErrors(['jurusans' => 'min'])
        ->assertSet('isCreatingSchool', true);
});

it('validates each jurusan name must not be empty for SMK', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('npsn_sekolah', '20327999')
        ->set('nama_sekolah', 'SMK Negeri 1 Test')
        ->set('jenis_sekolah', 'SMK')
        ->set('nip_operator', '199001012020011005')
        ->set('kuota_awal', '100')
        ->set('jurusans', [
            'Rekayasa Perangkat Lunak',
            '',
        ])
        ->call('createSchool')
        ->assertHasErrors(['jurusans.1' => 'required'])
        ->assertSet('isCreatingSchool', true);
});

it('successfully creates an SMK with jurusans and saves to database', function () {
    actingAs($this->admin);

    Livewire::test(Admin::class)
        ->call('setMenu', 'manage')
        ->call('openCreateSchoolForm')
        ->set('npsn_sekolah', '20327990')
        ->set('nama_sekolah', 'SMK Negeri 5 Surakarta')
        ->set('jenis_sekolah', 'SMK')
        ->set('nip_operator', '198901012018011003')
        ->set('password_operator', 'secret123')
        ->set('kuota_awal', '150')
        ->set('jurusans', [
            'Rekayasa Perangkat Lunak',
            'Teknik Komputer dan Jaringan',
            'Desain Komunikasi Visual',
        ])
        ->call('createSchool')
        ->assertHasNoErrors()
        ->assertSet('isCreatingSchool', false)
        ->assertSee('SMK Negeri 5 Surakarta')
        ->assertSee('Rekayasa Perangkat Lunak')
        ->assertSee('Teknik Komputer dan Jaringan')
        ->assertSee('Desain Komunikasi Visual');

    assertDatabaseHas('sekolahs', [
        'npsn' => '20327990',
        'nama_sekolah' => 'SMK Negeri 5 Surakarta',
        'jenis_sekolah' => 'SMK',
        'kuota' => 150,
    ]);

    $sekolah = Sekolah::where('npsn', '20327990')->first();
    expect($sekolah->jurusans)->toHaveCount(3)
        ->and($sekolah->jurusans->pluck('nama_jurusan')->all())->toEqual([
            'Rekayasa Perangkat Lunak',
            'Teknik Komputer dan Jaringan',
            'Desain Komunikasi Visual',
        ]);
});

it('displays statistics on admin dashboard beranda', function () {
    actingAs($this->admin);

    $sekolah = Sekolah::factory()->create([
        'jenis_sekolah' => 'SMA',
        'kuota' => 100,
    ]);

    RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Budi Siswa',
        'status_verifikasi' => 'terverifikasi',
        'is_activated' => true,
        'pilihan_sekolah_1_id' => $sekolah->id,
    ]);

    Livewire::test(Admin::class)
        ->call('setMenu', 'beranda')
        ->assertSee('Beranda SPMB Terpusat')
        ->assertSee('Total Pendaftar')
        ->assertSee('Akun Diaktivasi')
        ->assertSee('Total Sekolah')
        ->assertSee('Berkas Terverifikasi')
        ->assertSee('Menunggu Verifikasi')
        ->assertSee('Berkas Ditolak');
});
