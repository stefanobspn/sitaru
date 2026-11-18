<?php

use App\Enums\UserRole;
use App\Livewire\AktivasiAkun;
use App\Models\RegisteredSiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\get;

beforeEach(function () {
    Carbon::setTestNow('2026-06-05 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('renders the activation page successfully', function () {
    get('/aktivasi')
        ->assertOk()
        ->assertSee('Aktivasi Akun Siswa')
        ->assertSee('NISN')
        ->assertSee('Kode Aktivasi');
});

it('validates required fields on step 1', function () {
    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '')
        ->set('activation_code', '')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['nisn' => 'required', 'activation_code' => 'required'])
        ->assertSet('step', 1);
});

it('validates nisn format on step 1', function () {
    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '12345')
        ->set('activation_code', 'ACT-12345678')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['nisn' => 'digits']);
});

it('shows error if student with nisn is not found', function () {
    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '0000000000')
        ->set('activation_code', 'ACT-12345678')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['nisn'])
        ->assertSee('tidak ditemukan');
});

it('shows error if student is not verified yet', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'menunggu',
        'activation_code' => 'ACT-ABCDEF12',
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', $siswa->nisn)
        ->set('activation_code', 'ACT-ABCDEF12')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['activation_code'])
        ->assertSee('belum diverifikasi');
});

it('shows error if activation code does not match', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-ABCDEF12',
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', $siswa->nisn)
        ->set('activation_code', 'WRONG-CODE')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['activation_code'])
        ->assertSee('Kode aktivasi tidak sesuai');
});

it('shows error if student is already activated with user account', function () {
    $user = User::factory()->create([
        'nisn' => '1234567890',
        'role' => UserRole::Siswa,
    ]);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-ABCDEF12',
        'user_id' => $user->id,
        'is_activated' => true,
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', $siswa->nisn)
        ->set('activation_code', 'ACT-ABCDEF12')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['nisn'])
        ->assertSee('sudah pernah diaktivasi');
});

it('proceeds to step 2 when nisn and activation code match verified student', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Budi Santoso',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-ABCDEF12',
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-ABCDEF12')
        ->call('cekKodeAktivasi')
        ->assertHasNoErrors()
        ->assertSet('step', 2)
        ->assertSee('Budi Santoso')
        ->assertSee('Buat Password');
});

it('allows going back to step 1 from step 2', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-ABCDEF12',
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-ABCDEF12')
        ->call('cekKodeAktivasi')
        ->assertSet('step', 2)
        ->call('kembaliKeStep1')
        ->assertSet('step', 1)
        ->assertSee('Verifikasi Kode');
});

it('validates password requirement on step 2', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-ABCDEF12',
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-ABCDEF12')
        ->call('cekKodeAktivasi')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('simpanAktivasi')
        ->assertHasErrors(['password' => 'min'])
        ->set('password', 'password123')
        ->set('password_confirmation', 'different123')
        ->call('simpanAktivasi')
        ->assertHasErrors(['password' => 'confirmed']);
});

it('successfully activates account, creates user, and logs in student', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Dewi Sartika',
        'email' => 'dewi@example.test',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-XYZ12345',
        'is_activated' => false,
        'user_id' => null,
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-XYZ12345')
        ->call('cekKodeAktivasi')
        ->set('password', 'secret12345')
        ->set('password_confirmation', 'secret12345')
        ->call('simpanAktivasi')
        ->assertHasNoErrors()
        ->assertRedirect(route('siswa'));

    $siswa->refresh();
    expect($siswa->is_activated)->toBeTrue()
        ->and($siswa->user_id)->not->toBeNull();

    $user = User::where('nisn', '1234567890')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Dewi Sartika')
        ->and($user->email)->toBe('dewi@example.test')
        ->and($user->role)->toBe(UserRole::Siswa)
        ->and(Hash::check('secret12345', $user->password))->toBeTrue()
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

it('allows activation when orphaned user exists in users table but registered_siswa is unactivated', function () {
    // Simulate user table having an orphaned record from a previous run
    User::factory()->create([
        'nisn' => '1234567890',
        'name' => 'Old Student Record',
        'role' => UserRole::Siswa,
    ]);

    // Student registered afresh after registered_siswas table was truncated
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Dewi Sartika Baru',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-NEW12345',
        'is_activated' => false,
        'user_id' => null,
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-NEW12345')
        ->call('cekKodeAktivasi')
        ->assertHasNoErrors()
        ->assertSet('step', 2)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('simpanAktivasi')
        ->assertHasNoErrors()
        ->assertRedirect(route('siswa'));

    $siswa->refresh();
    expect($siswa->is_activated)->toBeTrue()
        ->and($siswa->user_id)->not->toBeNull();

    $user = User::where('nisn', '1234567890')->first();
    expect($user->name)->toBe('Dewi Sartika Baru')
        ->and(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('allows activation for newly verified student without linked user account even if is_activated was true in db', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Budi Santoso',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-NEWVERIF',
        'is_activated' => true,
        'user_id' => null,
    ]);

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-NEWVERIF')
        ->call('cekKodeAktivasi')
        ->assertHasNoErrors()
        ->assertSet('step', 2);
});

it('prevents account activation on 15 June 2026 when activation schedule has ended', function () {
    Carbon::setTestNow('2026-06-15 10:00:00');

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-NEWVERIF',
        'is_activated' => false,
        'user_id' => null,
    ]);

    get('/aktivasi')
        ->assertOk()
        ->assertSee('Telah Ditutup (Selesai 13 Juni 2026)');

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', $siswa->nisn)
        ->set('activation_code', 'ACT-NEWVERIF')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['activation_code'])
        ->assertSee('telah ditutup pada tanggal 13 Juni 2026')
        ->assertSet('step', 1);
});

it('prevents account activation before 4 June 2026 when schedule has not started', function () {
    Carbon::setTestNow('2026-06-02 10:00:00');

    get('/aktivasi')
        ->assertOk()
        ->assertSee('Belum Dibuka (Mulai 4 Juni 2026)');

    Livewire::test(AktivasiAkun::class)
        ->set('nisn', '1234567890')
        ->set('activation_code', 'ACT-NEWVERIF')
        ->call('cekKodeAktivasi')
        ->assertHasErrors(['activation_code'])
        ->assertSee('belum dibuka')
        ->assertSet('step', 1);
});
