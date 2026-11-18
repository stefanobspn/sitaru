<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\RegisteredSiswa;
use App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AktivasiAkun extends Component
{
    public int $step = 1;

    public string $nisn = '';

    public string $activation_code = '';

    public ?RegisteredSiswa $siswa = null;

    public string $password = '';

    public string $password_confirmation = '';

    public function cekKodeAktivasi(): void
    {
        if (! ScheduleService::isAktivasiAkunOpen()) {
            $this->addError('activation_code', ScheduleService::getAgendaErrorMessage('aktivasi'));

            return;
        }

        $this->validate([
            'nisn' => ['required', 'numeric', 'digits:10'],
            'activation_code' => ['required', 'string'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits' => 'NISN harus berjumlah 10 digit.',
            'activation_code.required' => 'Kode aktivasi wajib diisi.',
        ]);

        $found = RegisteredSiswa::where('nisn', $this->nisn)->first();

        if (! $found) {
            $this->addError('nisn', 'Data pendaftaran dengan NISN tersebut tidak ditemukan.');

            return;
        }

        if ($found->user_id !== null && $found->user !== null) {
            $this->addError('nisn', 'Akun untuk NISN ini sudah pernah diaktivasi sebelumnya. Silahkan langsung login.');

            return;
        }

        if ($found->status_verifikasi !== 'terverifikasi') {
            $this->addError('activation_code', 'Berkas pendaftaran Anda belum diverifikasi oleh Operator Sekolah. Silahkan lakukan verifikasi berkas terlebih dahulu.');

            return;
        }

        if (trim(strtoupper($this->activation_code)) !== trim(strtoupper((string) $found->activation_code))) {
            $this->addError('activation_code', 'Kode aktivasi tidak sesuai. Silahkan periksa lembar Bukti Verifikasi yang diberikan oleh Operator.');

            return;
        }

        $this->siswa = $found;
        $this->step = 2;
        $this->resetValidation();
    }

    public function kembaliKeStep1(): void
    {
        $this->step = 1;
        $this->reset(['password', 'password_confirmation']);
        $this->resetValidation();
    }

    public function simpanAktivasi()
    {
        if (! ScheduleService::isAktivasiAkunOpen()) {
            session()->flash('error', ScheduleService::getAgendaErrorMessage('aktivasi'));

            return;
        }

        if (! $this->siswa) {
            $this->step = 1;

            return;
        }

        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal berjumlah 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = DB::transaction(function () {
            $user = User::updateOrCreate(
                ['nisn' => $this->siswa->nisn],
                [
                    'name' => $this->siswa->nama_lengkap,
                    'email' => $this->siswa->email,
                    'role' => UserRole::Siswa,
                    'password' => Hash::make($this->password),
                ]
            );

            $this->siswa->update([
                'user_id' => $user->id,
                'is_activated' => true,
            ]);

            return $user;
        });

        Auth::login($user);

        session()->flash('success', 'Akun Anda berhasil diaktivasi! Selamat datang di Portal Calon Siswa.');

        return redirect()->route('siswa');
    }

    public function render(): View
    {
        return view('livewire.aktivasi-akun', [
            'isAktivasiOpen' => ScheduleService::isAktivasiAkunOpen(),
            'aktivasiStatus' => ScheduleService::getAgendaStatus('aktivasi'),
            'agendaErrorMessage' => ScheduleService::getAgendaErrorMessage('aktivasi'),
        ]);
    }
}
