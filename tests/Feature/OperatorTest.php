<?php

use App\Enums\UserRole;
use App\Livewire\Operator;
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
    Carbon::setTestNow('2026-06-05 10:00:00');

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

afterEach(function () {
    Carbon::setTestNow();
});

it('shows the operator login form for unauthenticated users', function () {
    get('/operator')
        ->assertOk()
        ->assertSee('Login Operator')
        ->assertSee('NIP')
        ->assertSee('Password');
});

it('validates required fields on operator login', function () {
    Livewire::test(Operator::class)
        ->set('nip', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['nip' => 'required', 'password' => 'required']);
});

it('fails login with invalid password', function () {
    Livewire::test(Operator::class)
        ->set('nip', '198501012010011001')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['nip'])
        ->assertSee('NIP atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('fails login with non-existent nip', function () {
    Livewire::test(Operator::class)
        ->set('nip', '999999999999999999')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['nip'])
        ->assertSee('NIP atau password salah');

    expect(Auth::check())->toBeFalse();
});

it('successfully logs in operator and displays the dashboard', function () {
    Livewire::test(Operator::class)
        ->set('nip', '198501012010011001')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertSee('Beranda')
        ->assertSee('Logout');

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->role)->toBe(UserRole::Operator);
});

it('shows dashboard directly when already authenticated as operator', function () {
    actingAs($this->operator);

    get('/operator')
        ->assertOk()
        ->assertSee('Beranda')
        ->assertSee('Logout');
});

it('allows operator to logout from the dashboard', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('logout')
        ->assertSee('Login Operator');

    expect(Auth::check())->toBeFalse();
});

it('prevents operator from accessing admin dashboard directly', function () {
    actingAs($this->operator);

    get('/admin')
        ->assertOk()
        ->assertSee('Login Admin')
        ->assertDontSee('Logout');
});

it('prevents admin from accessing operator dashboard directly', function () {
    actingAs($this->admin);

    get('/operator')
        ->assertOk()
        ->assertSee('Login Operator')
        ->assertDontSee('Logout');
});

it('supports remember me when logging in as operator', function () {
    Livewire::test(Operator::class)
        ->set('nip', '198501012010011001')
        ->set('password', 'password')
        ->set('remember', true)
        ->call('login')
        ->assertHasNoErrors();

    expect(Auth::check())->toBeTrue();
});

it('can switch active menu to manage in operator dashboard', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('setMenu', 'manage')
        ->assertSet('activeMenu', 'manage')
        ->assertSet('isManageMenuClicked', true)
        ->assertSee('Manajemen SPMB')
        ->assertDontSee('Selamat Datang');
});

it('can open and close verification form', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('setMenu', 'manage')
        ->call('openVerificationForm')
        ->assertSet('isVerifying', true)
        ->assertSee('Verifikasi Pendaftaran Siswa')
        ->assertSee('Cari Data')
        ->call('closeVerificationForm')
        ->assertSet('isVerifying', false)
        ->assertDontSee('Verifikasi Pendaftaran Siswa');
});

it('validates search nisn when searching for student', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '')
        ->call('cariSiswa')
        ->assertHasErrors(['searchNisn' => 'required'])
        ->set('searchNisn', 'abc')
        ->call('cariSiswa')
        ->assertHasErrors(['searchNisn' => 'numeric'])
        ->set('searchNisn', '12345')
        ->call('cariSiswa')
        ->assertHasErrors(['searchNisn' => 'digits']);
});

it('shows error if student with nisn is not found', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '0000000000')
        ->call('cariSiswa')
        ->assertHasErrors(['searchNisn'])
        ->assertSee('tidak ditemukan');
});

it('finds and displays student data when searching by valid nisn', function () {
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Budi Santoso',
    ]);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->assertHasNoErrors()
        ->assertSet('foundSiswa.id', $siswa->id)
        ->assertSee('Budi Santoso')
        ->assertSee($siswa->nomor_pendaftaran)
        ->assertSee('Saya paham pilihan saya, saya bersedia bertanggung jawab');
});

it('requires responsibility confirmation checkbox before verifying', function () {
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
    ]);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->set('persetujuanTanggungJawab', false)
        ->call('verifikasiSiswa')
        ->assertHasErrors(['persetujuanTanggungJawab' => 'accepted']);
});

it('successfully verifies student, updates database with activation code and operator nip, and downloads pdf', function () {
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Ahmad Dahlan',
        'nomor_pendaftaran' => 'REG-2026-TEST99',
        'is_activated' => false,
        'status_verifikasi' => 'menunggu',
    ]);

    $component = Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->set('persetujuanTanggungJawab', true)
        ->call('verifikasiSiswa');

    $component->assertHasNoErrors()
        ->assertFileDownloaded("bukti-pendaftaran-{$siswa->nomor_pendaftaran}.pdf");

    $siswa->refresh();
    expect($siswa->is_activated)->toBeFalse()
        ->and($siswa->status_verifikasi)->toBe('terverifikasi')
        ->and($siswa->activation_code)->not->toBeNull()
        ->and($siswa->activation_code)->toStartWith('ACT-')
        ->and($siswa->operator_nip)->toBe($this->operator->nip);
});

it('requires responsibility confirmation checkbox before rejecting', function () {
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
    ]);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->set('persetujuanTanggungJawab', false)
        ->call('tolakSiswa')
        ->assertHasErrors(['persetujuanTanggungJawab' => 'accepted']);
});

it('successfully rejects student and updates database with operator nip', function () {
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'nama_lengkap' => 'Siti Aminah',
        'is_activated' => false,
        'status_verifikasi' => 'menunggu',
    ]);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->set('persetujuanTanggungJawab', true)
        ->call('tolakSiswa')
        ->assertHasNoErrors();

    $siswa->refresh();
    expect($siswa->is_activated)->toBeFalse()
        ->and($siswa->status_verifikasi)->toBe('ditolak')
        ->and($siswa->operator_nip)->toBe($this->operator->nip);
});

it('renders pdf bukti pendaftaran with activation code and operator nip', function () {
    $siswa = RegisteredSiswa::factory()->create([
        'nomor_pendaftaran' => 'REG-2026-TEST88',
        'activation_code' => 'ACT-TEST1234',
        'operator_nip' => '198501012010011001',
    ]);

    $view = view('pdf.bukti-pendaftaran', [
        'siswa' => $siswa,
        'activationCode' => $siswa->activation_code,
        'operatorNip' => $siswa->operator_nip,
    ])->render();

    expect($view)
        ->toContain('Activation Code')
        ->toContain('ACT-TEST1234')
        ->toContain('NIP Operator')
        ->toContain('198501012010011001');
});

it('can open and close daftar ulang section in operator dashboard', function () {
    actingAs($this->operator);

    Livewire::test(Operator::class)
        ->call('setMenu', 'manage')
        ->call('openDaftarUlang')
        ->assertSet('isDaftarUlang', true)
        ->assertSee('Konfirmasi Daftar Ulang Siswa')
        ->call('closeDaftarUlang')
        ->assertSet('isDaftarUlang', false)
        ->assertDontSee('Konfirmasi Daftar Ulang Siswa');
});

it('allows operator to confirm and cancel daftar ulang for accepted students during official schedule', function () {
    Carbon::setTestNow('2026-06-26 10:00:00');

    actingAs($this->operator);

    $sekolah = Sekolah::factory()->create([
        'operator_id' => $this->operator->id,
        'kuota' => 10,
    ]);

    $siswa = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Galih Firmansyah',
        'rata_rata_nilai_rapor' => 90.00,
        'pilihan_sekolah_1_id' => $sekolah->id,
        'is_daftar_ulang' => false,
    ]);

    Livewire::test(Operator::class)
        ->call('openDaftarUlang')
        ->assertSee('Galih Firmansyah')
        ->assertSee('Konfirmasi Hadir')
        ->call('konfirmasiDaftarUlang', $siswa->id)
        ->assertSee('Sudah Daftar Ulang');

    expect($siswa->fresh()->is_daftar_ulang)->toBeTrue();

    Livewire::test(Operator::class)
        ->call('openDaftarUlang')
        ->call('batalkanDaftarUlang', $siswa->id)
        ->assertSee('Konfirmasi Hadir');

    expect($siswa->fresh()->is_daftar_ulang)->toBeFalse();

    Carbon::setTestNow();
});

it('cannot confirm daftar ulang before 26 June 2026', function () {
    Carbon::setTestNow('2026-06-25 10:00:00');

    actingAs($this->operator);

    $sekolah = Sekolah::factory()->create([
        'operator_id' => $this->operator->id,
        'kuota' => 10,
    ]);

    $siswa = RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Galih Firmansyah',
        'rata_rata_nilai_rapor' => 90.00,
        'pilihan_sekolah_1_id' => $sekolah->id,
        'is_daftar_ulang' => false,
    ]);

    Livewire::test(Operator::class)
        ->call('openDaftarUlang')
        ->assertSee('Galih Firmansyah')
        ->assertSee('Dibuka 26 Juni')
        ->assertDontSee('Konfirmasi Hadir')
        ->call('konfirmasiDaftarUlang', $siswa->id)
        ->assertSee('Konfirmasi daftar ulang belum dapat dilakukan sebelum jadwal resminya');

    expect($siswa->fresh()->is_daftar_ulang)->toBeFalse();

    Carbon::setTestNow();
});

it('displays school statistics and applicant count on operator dashboard beranda', function () {
    actingAs($this->operator);

    $sekolah = Sekolah::factory()->create([
        'operator_id' => $this->operator->id,
        'kuota' => 20,
    ]);

    RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa Pilihan Satu',
        'pilihan_sekolah_1_id' => $sekolah->id,
    ]);

    RegisteredSiswa::factory()->create([
        'nama_lengkap' => 'Siswa Pilihan Dua',
        'pilihan_sekolah_2_id' => $sekolah->id,
    ]);

    Livewire::test(Operator::class)
        ->call('setMenu', 'beranda')
        ->assertSee('Peminat Pilihan 1')
        ->assertSee('Peminat Pilihan 2')
        ->assertSee('Daya Tampung (Kuota)')
        ->assertSee('Status Daftar Ulang Siswa Lolos');
});

it('can open and close edit kuota form in operator dashboard', function () {
    actingAs($this->operator);

    $sekolah = Sekolah::factory()->create([
        'operator_id' => $this->operator->id,
        'kuota' => 15,
    ]);

    Livewire::test(Operator::class)
        ->call('setMenu', 'manage')
        ->call('openEditKuotaForm')
        ->assertSet('isEditingKuota', true)
        ->assertSet('kuota', 15)
        ->assertSee('Edit Kuota Penerimaan Siswa')
        ->call('closeEditKuotaForm')
        ->assertSet('isEditingKuota', false)
        ->assertDontSee('Edit Kuota Penerimaan Siswa');
});

it('allows operator to update school kuota with validation', function () {
    actingAs($this->operator);

    $sekolah = Sekolah::factory()->create([
        'operator_id' => $this->operator->id,
        'kuota' => 20,
    ]);

    // Validasi gagal bila kuota kosong atau negatif/0
    Livewire::test(Operator::class)
        ->call('openEditKuotaForm')
        ->set('kuota', 0)
        ->call('simpanKuota')
        ->assertHasErrors(['kuota' => 'min'])
        ->assertSet('isEditingKuota', true);

    expect($sekolah->fresh()->kuota)->toBe(20);

    // Sukses mengubah kuota
    Livewire::test(Operator::class)
        ->call('openEditKuotaForm')
        ->set('kuota', 45)
        ->call('simpanKuota')
        ->assertHasNoErrors()
        ->assertSet('isEditingKuota', false)
        ->assertSee('45 siswa');

    expect($sekolah->fresh()->kuota)->toBe(45);
});

it('shows verified students in riwayat verifikasi table for the verifying operator', function () {
    actingAs($this->operator);

    $studentUser = User::factory()->create([
        'nisn' => '0081234599',
        'role' => UserRole::Siswa,
        'password' => Hash::make('oldpassword'),
    ]);

    $siswaVerif = RegisteredSiswa::factory()->create([
        'user_id' => $studentUser->id,
        'nisn' => '0081234599',
        'nama_lengkap' => 'Siswa Terverifikasi Saya',
        'operator_nip' => $this->operator->nip,
        'status_verifikasi' => 'terverifikasi',
        'is_activated' => true,
    ]);

    Livewire::test(Operator::class)
        ->call('setMenu', 'manage')
        ->call('openRiwayatVerifikasi')
        ->assertSet('isRiwayatVerifikasi', true)
        ->assertSee('Riwayat Verifikasi dan Akun Calon Siswa')
        ->assertSee('Siswa Terverifikasi Saya')
        ->assertSee('0081234599')
        ->assertSee('Akun Aktif')
        ->assertSee('Reset Password');
});

it('only displays students verified by the current operator, not by other operators', function () {
    actingAs($this->operator);

    // Siswa diverifikasi oleh operator lain
    $siswaOther = RegisteredSiswa::factory()->create([
        'nisn' => '0089999999',
        'nama_lengkap' => 'Siswa Operator Lain',
        'operator_nip' => '199999999999999999',
        'status_verifikasi' => 'terverifikasi',
        'is_activated' => true,
    ]);

    Livewire::test(Operator::class)
        ->call('openRiwayatVerifikasi')
        ->assertDontSee('Siswa Operator Lain')
        ->assertDontSee('0089999999');
});

it('allows the verifying operator to reset student password to default or custom password', function () {
    actingAs($this->operator);

    $studentUser = User::factory()->create([
        'nisn' => '0081234588',
        'role' => UserRole::Siswa,
        'password' => Hash::make('oldpassword123'),
    ]);

    $siswa = RegisteredSiswa::factory()->create([
        'user_id' => $studentUser->id,
        'nisn' => '0081234588',
        'nama_lengkap' => 'Farhan Pratama',
        'operator_nip' => $this->operator->nip,
        'status_verifikasi' => 'terverifikasi',
        'is_activated' => true,
    ]);

    // Buka modal dan reset ke password kustom
    Livewire::test(Operator::class)
        ->call('openRiwayatVerifikasi')
        ->call('openResetPasswordModal', $siswa->id)
        ->assertSet('resetTargetSiswaId', $siswa->id)
        ->set('newPassword', 'passwordBaru123')
        ->call('resetPasswordSiswa', $siswa->id)
        ->assertHasNoErrors()
        ->assertSee('Password untuk akun siswa Farhan Pratama (NISN: 0081234588) berhasil direset menjadi: passwordBaru123');

    // Verifikasi di database bahwa password siswa telah di-hash dengan password baru
    $studentUser->refresh();
    expect(Hash::check('passwordBaru123', $studentUser->password))->toBeTrue();
    expect(Hash::check('oldpassword123', $studentUser->password))->toBeFalse();
});

it('forbids an operator from resetting password of a student verified by another operator', function () {
    actingAs($this->operator);

    $otherOperator = User::factory()->create([
        'nip' => '198001012005011002',
        'role' => UserRole::Operator,
    ]);

    $studentUser = User::factory()->create([
        'nisn' => '0081234577',
        'role' => UserRole::Siswa,
        'password' => Hash::make('originalpassword'),
    ]);

    $siswaOther = RegisteredSiswa::factory()->create([
        'user_id' => $studentUser->id,
        'nisn' => '0081234577',
        'nama_lengkap' => 'Siswa Milik Operator Lain',
        'operator_nip' => $otherOperator->nip,
        'status_verifikasi' => 'terverifikasi',
        'is_activated' => true,
    ]);

    Livewire::test(Operator::class)
        ->call('resetPasswordSiswa', $siswaOther->id)
        ->assertHasErrors(['resetPassword'])
        ->assertSee('Anda tidak memiliki hak akses untuk mereset password siswa ini');

    // Password tidak berubah
    $studentUser->refresh();
    expect(Hash::check('originalpassword', $studentUser->password))->toBeTrue();
});

it('shows error when attempting to reset password for a student who has not activated an account', function () {
    actingAs($this->operator);

    $siswaBelumAktivasi = RegisteredSiswa::factory()->create([
        'user_id' => null,
        'nisn' => '0081234566',
        'nama_lengkap' => 'Siswa Belum Aktivasi',
        'operator_nip' => $this->operator->nip,
        'status_verifikasi' => 'terverifikasi',
        'activation_code' => 'ACT-BELUMAKTIF',
        'is_activated' => false,
    ]);

    Livewire::test(Operator::class)
        ->call('resetPasswordSiswa', $siswaBelumAktivasi->id)
        ->assertHasErrors(['resetPassword'])
        ->assertSee('belum melakukan aktivasi akun')
        ->assertSee('ACT-BELUMAKTIF');
});

it('cannot verify student on 15 June 2026 when verification schedule has ended', function () {
    Carbon::setTestNow('2026-06-15 10:00:00');
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'menunggu',
    ]);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->assertSee('Tahap verifikasi berkas pendaftaran telah ditutup pada tanggal 12 Juni 2026.')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->set('persetujuanTanggungJawab', true)
        ->call('verifikasiSiswa')
        ->assertSee('Tahap verifikasi berkas pendaftaran telah ditutup pada tanggal 12 Juni 2026.');

    expect($siswa->fresh()->status_verifikasi)->toBe('menunggu');
});

it('cannot verify student before 3 June 2026 when verification schedule has not started', function () {
    Carbon::setTestNow('2026-06-01 10:00:00');
    actingAs($this->operator);

    $siswa = RegisteredSiswa::factory()->create([
        'nisn' => '1234567890',
        'status_verifikasi' => 'menunggu',
    ]);

    Livewire::test(Operator::class)
        ->call('openVerificationForm')
        ->assertSee('Tahap verifikasi berkas pendaftaran belum dibuka.')
        ->set('searchNisn', '1234567890')
        ->call('cariSiswa')
        ->set('persetujuanTanggungJawab', true)
        ->call('verifikasiSiswa')
        ->assertSee('Tahap verifikasi berkas pendaftaran belum dibuka.');

    expect($siswa->fresh()->status_verifikasi)->toBe('menunggu');
});
