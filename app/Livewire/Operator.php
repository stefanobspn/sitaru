<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\RegisteredSiswa;
use App\Services\ScheduleService;
use App\Services\SeleksiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Operator extends Component
{
    #[Url(as: 'menu')]
    public string $activeMenu = 'beranda';

    public string $nip = '';

    public string $password = '';

    public bool $remember = false;

    public bool $isManageMenuClicked = false;

    public bool $isVerifying = false;

    public bool $isDaftarUlang = false;

    public bool $isEditingKuota = false;

    public bool $isRiwayatVerifikasi = false;

    public string $searchRiwayat = '';

    public ?int $resetTargetSiswaId = null;

    public string $newPassword = '';

    public ?string $resetSuccessMessage = null;

    public ?string $resetErrorMessage = null;

    public ?int $kuota = null;

    public string $searchNisn = '';

    public ?RegisteredSiswa $foundSiswa = null;

    public bool $persetujuanTanggungJawab = false;

    public function login(): void
    {
        $this->validate([
            'nip' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['nip' => $this->nip, 'password' => $this->password, 'role' => UserRole::Operator], $this->remember)) {
            session()->regenerate();
            $this->reset(['password']);

            return;
        }

        $this->addError('nip', 'NIP atau password salah, atau Anda bukan Operator.');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->reset(['nip', 'password', 'remember', 'isManageMenuClicked', 'isVerifying', 'isDaftarUlang', 'isEditingKuota', 'isRiwayatVerifikasi', 'searchRiwayat', 'resetTargetSiswaId', 'newPassword', 'resetSuccessMessage', 'kuota', 'searchNisn', 'persetujuanTanggungJawab']);
        $this->foundSiswa = null;
    }

    public function setMenu(string $menu): void
    {
        $this->activeMenu = $menu;
        $this->isManageMenuClicked = ($menu === 'manage');
        $this->isVerifying = false;
        $this->isDaftarUlang = false;
        $this->isEditingKuota = false;
        $this->isRiwayatVerifikasi = false;
        $this->closeResetPasswordModal();
        $this->resetVerificationState();
    }

    public function openVerificationForm(): void
    {
        $this->activeMenu = 'manage';
        $this->isManageMenuClicked = true;
        $this->isVerifying = true;
        $this->isDaftarUlang = false;
        $this->isEditingKuota = false;
        $this->isRiwayatVerifikasi = false;
        $this->closeResetPasswordModal();
        $this->resetVerificationState();
    }

    public function closeVerificationForm(): void
    {
        $this->isVerifying = false;
        $this->closeResetPasswordModal();
        $this->resetVerificationState();
    }

    public function openDaftarUlang(): void
    {
        $this->activeMenu = 'manage';
        $this->isManageMenuClicked = true;
        $this->isVerifying = false;
        $this->isDaftarUlang = true;
        $this->isEditingKuota = false;
        $this->isRiwayatVerifikasi = false;
        $this->closeResetPasswordModal();
        $this->resetVerificationState();
    }

    public function closeDaftarUlang(): void
    {
        $this->isDaftarUlang = false;
    }

    public function openEditKuotaForm(): void
    {
        $this->activeMenu = 'manage';
        $this->isManageMenuClicked = true;
        $this->isVerifying = false;
        $this->isDaftarUlang = false;
        $this->isEditingKuota = true;
        $this->isRiwayatVerifikasi = false;
        $this->closeResetPasswordModal();
        $this->kuota = Auth::user()?->sekolah?->kuota;
        $this->resetValidation();
    }

    public function closeEditKuotaForm(): void
    {
        $this->isEditingKuota = false;
        $this->resetValidation();
    }

    public function openRiwayatVerifikasi(): void
    {
        $this->activeMenu = 'manage';
        $this->isManageMenuClicked = true;
        $this->isVerifying = false;
        $this->isDaftarUlang = false;
        $this->isEditingKuota = false;
        $this->isRiwayatVerifikasi = true;
        $this->closeResetPasswordModal();
        $this->resetVerificationState();
    }

    public function closeRiwayatVerifikasi(): void
    {
        $this->isRiwayatVerifikasi = false;
        $this->closeResetPasswordModal();
    }

    public function openResetPasswordModal(int $siswaId): void
    {
        $this->resetTargetSiswaId = $siswaId;
        $this->newPassword = 'password123';
        $this->resetSuccessMessage = null;
        $this->resetErrorMessage = null;
        $this->resetValidation();
    }

    public function closeResetPasswordModal(): void
    {
        $this->resetTargetSiswaId = null;
        $this->newPassword = '';
        $this->resetErrorMessage = null;
        $this->resetValidation();
    }

    public function resetPasswordSiswa(int $siswaId): void
    {
        $this->resetErrorMessage = null;
        $operatorNip = Auth::user()?->nip;
        $siswa = RegisteredSiswa::with('user')->find($siswaId);

        if (! $siswa) {
            $this->resetErrorMessage = 'Data siswa tidak ditemukan.';
            $this->addError('resetPassword', $this->resetErrorMessage);
            session()->flash('error', $this->resetErrorMessage);

            return;
        }

        if ($siswa->operator_nip !== $operatorNip) {
            $this->resetErrorMessage = 'Anda tidak memiliki hak akses untuk mereset password siswa ini karena Anda bukan operator yang memverifikasi akun siswa tersebut.';
            $this->addError('resetPassword', $this->resetErrorMessage);
            session()->flash('error', $this->resetErrorMessage);

            return;
        }

        if (! $siswa->user) {
            $this->resetErrorMessage = "Siswa {$siswa->nama_lengkap} belum melakukan aktivasi akun (belum membuat password). Kode aktivasi siswa adalah: {$siswa->activation_code}.";
            $this->addError('resetPassword', $this->resetErrorMessage);
            session()->flash('error', $this->resetErrorMessage);

            return;
        }

        $passwordToSet = trim($this->newPassword) !== '' ? $this->newPassword : 'password123';

        if (strlen($passwordToSet) < 6) {
            $this->addError('newPassword', 'Password baru minimal harus 6 karakter.');

            return;
        }

        $siswa->user->update([
            'password' => Hash::make($passwordToSet),
        ]);

        $this->resetSuccessMessage = "Password untuk akun siswa {$siswa->nama_lengkap} (NISN: {$siswa->nisn}) berhasil direset menjadi: {$passwordToSet}";
        session()->flash('success', "Password akun siswa {$siswa->nama_lengkap} berhasil direset.");
        $this->resetTargetSiswaId = null;
        $this->newPassword = '';
    }

    public function simpanKuota(): void
    {
        $this->validate([
            'kuota' => ['required', 'integer', 'min:1', 'max:5000'],
        ], [
            'kuota.required' => 'Kuota sekolah wajib diisi.',
            'kuota.integer' => 'Kuota sekolah harus berupa angka bulat.',
            'kuota.min' => 'Kuota sekolah minimal 1 siswa.',
            'kuota.max' => 'Kuota sekolah maksimal 5000 siswa.',
        ]);

        $sekolah = Auth::user()?->sekolah;
        if ($sekolah) {
            $sekolah->update([
                'kuota' => $this->kuota,
            ]);
            session()->flash('success', "Kuota penerimaan sekolah {$sekolah->nama_sekolah} berhasil diperbarui menjadi {$this->kuota} siswa.");
        }

        $this->isEditingKuota = false;
    }

    public function konfirmasiDaftarUlang(int $siswaId): void
    {
        if (! ScheduleService::isDaftarUlangOpen()) {
            session()->flash('error', 'Konfirmasi daftar ulang belum dapat dilakukan sebelum jadwal resminya (26 Juni 2026).');

            return;
        }

        $siswa = RegisteredSiswa::find($siswaId);
        if ($siswa) {
            $siswa->update(['is_daftar_ulang' => true]);
            session()->flash('success', "Siswa {$siswa->nama_lengkap} berhasil dikonfirmasi daftar ulang.");
        }
    }

    public function batalkanDaftarUlang(int $siswaId): void
    {
        if (! ScheduleService::isDaftarUlangOpen()) {
            session()->flash('error', 'Pembatalan daftar ulang hanya dapat dilakukan pada jadwal resmi (26 Juni 2026).');

            return;
        }

        $siswa = RegisteredSiswa::find($siswaId);
        if ($siswa) {
            $siswa->update(['is_daftar_ulang' => false]);
            session()->flash('info', "Konfirmasi daftar ulang siswa {$siswa->nama_lengkap} dibatalkan.");
        }
    }

    public function resetVerificationState(): void
    {
        $this->reset(['searchNisn', 'persetujuanTanggungJawab']);
        $this->foundSiswa = null;
        $this->resetValidation();
    }

    public function cariSiswa(): void
    {
        $this->validate([
            'searchNisn' => ['required', 'numeric', 'digits:10'],
        ], [
            'searchNisn.required' => 'NISN wajib diisi.',
            'searchNisn.numeric' => 'NISN harus berupa angka.',
            'searchNisn.digits' => 'NISN harus berjumlah 10 digit.',
        ]);

        $this->persetujuanTanggungJawab = false;
        $this->foundSiswa = RegisteredSiswa::where('nisn', $this->searchNisn)->first();

        if (! $this->foundSiswa) {
            $this->addError('searchNisn', 'Data pendaftaran dengan NISN '.$this->searchNisn.' tidak ditemukan.');
        }
    }

    public function verifikasiSiswa(): ?StreamedResponse
    {
        if (! ScheduleService::isVerifikasiOpen()) {
            session()->flash('error', ScheduleService::getAgendaErrorMessage('verifikasi'));

            return null;
        }

        if (! $this->foundSiswa) {
            return null;
        }

        $this->validate([
            'persetujuanTanggungJawab' => ['accepted'],
        ], [
            'persetujuanTanggungJawab.accepted' => 'Anda harus mencentang pernyataan bahwa Anda paham dan bersedia bertanggung jawab.',
        ]);

        $operatorNip = Auth::user()?->nip ?? '';
        $activationCode = $this->foundSiswa->activation_code ?? 'ACT-'.strtoupper(Str::random(8));

        $this->foundSiswa->update([
            'is_activated' => false,
            'status_verifikasi' => 'terverifikasi',
            'activation_code' => $activationCode,
            'operator_nip' => $operatorNip,
        ]);

        $this->foundSiswa->refresh();
        $this->persetujuanTanggungJawab = false;
        session()->flash('success', 'Data pendaftaran siswa '.$this->foundSiswa->nama_lengkap.' berhasil diverifikasi.');

        $pdf = Pdf::loadView('pdf.bukti-pendaftaran', [
            'siswa' => $this->foundSiswa,
            'activationCode' => $activationCode,
            'operatorNip' => $operatorNip,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "bukti-pendaftaran-{$this->foundSiswa->nomor_pendaftaran}.pdf");
    }

    public function tolakSiswa(): void
    {
        if (! ScheduleService::isVerifikasiOpen()) {
            session()->flash('error', ScheduleService::getAgendaErrorMessage('verifikasi'));

            return;
        }

        if (! $this->foundSiswa) {
            return;
        }

        $this->validate([
            'persetujuanTanggungJawab' => ['accepted'],
        ], [
            'persetujuanTanggungJawab.accepted' => 'Anda harus mencentang pernyataan bahwa Anda paham dan bersedia bertanggung jawab.',
        ]);

        $operatorNip = Auth::user()?->nip ?? '';

        $this->foundSiswa->update([
            'is_activated' => false,
            'status_verifikasi' => 'ditolak',
            'operator_nip' => $operatorNip,
        ]);

        $this->foundSiswa->refresh();
        $this->persetujuanTanggungJawab = false;
        session()->flash('info', 'Pendaftaran siswa '.$this->foundSiswa->nama_lengkap.' telah ditolak.');
    }

    public function render(): View
    {
        if (Auth::check() && Auth::user()?->role === UserRole::Operator) {
            $sekolah = Auth::user()->sekolah;
            $siswaDaftarUlangList = collect();

            $pendaftarPilihan1Count = 0;
            $pendaftarPilihan2Count = 0;
            $kuotaSekolah = 0;
            $jurusanStats = collect();
            $isDaftarUlangOpen = ScheduleService::isDaftarUlangOpen();

            if ($sekolah) {
                $penempatan = SeleksiService::hitungPenempatan();
                $siswaDaftarUlangList = $penempatan['admitted_by_school'][$sekolah->id] ?? collect();

                $pendaftarPilihan1Count = RegisteredSiswa::where('pilihan_sekolah_1_id', $sekolah->id)->count();
                $pendaftarPilihan2Count = RegisteredSiswa::where('pilihan_sekolah_2_id', $sekolah->id)->count();
                $kuotaSekolah = (int) $sekolah->kuota;

                if ($sekolah->jenis_sekolah === 'SMK') {
                    $jurusanStats = $sekolah->jurusans->map(function ($jurusan) use ($siswaDaftarUlangList) {
                        return [
                            'id' => $jurusan->id,
                            'nama_jurusan' => $jurusan->nama_jurusan,
                            'peminat_1' => RegisteredSiswa::where('pilihan_jurusan_1_id', $jurusan->id)->count(),
                            'peminat_2' => RegisteredSiswa::where('pilihan_jurusan_2_id', $jurusan->id)->count(),
                            'diterima' => $siswaDaftarUlangList->where('diterima_jurusan_id', $jurusan->id)->count(),
                        ];
                    });
                }
            }

            $sudahDaftarUlangCount = $siswaDaftarUlangList->where('is_daftar_ulang', true)->count();
            $belumDaftarUlangCount = $siswaDaftarUlangList->where('is_daftar_ulang', false)->count();
            $diverifikasiOlehOperatorCount = RegisteredSiswa::where('operator_nip', Auth::user()->nip)->count();

            $riwayatQuery = RegisteredSiswa::query()
                ->with('user')
                ->where('operator_nip', Auth::user()->nip)
                ->where('status_verifikasi', 'terverifikasi')
                ->latest('updated_at');

            if (trim($this->searchRiwayat) !== '') {
                $search = trim($this->searchRiwayat);
                $riwayatQuery->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('nomor_pendaftaran', 'like', "%{$search}%");
                });
            }

            return view('livewire.operator.dashboard', [
                'siswaDaftarUlangList' => $siswaDaftarUlangList,
                'riwayatSiswaList' => $riwayatQuery->get(),
                'pendaftarPilihan1Count' => $pendaftarPilihan1Count,
                'pendaftarPilihan2Count' => $pendaftarPilihan2Count,
                'kuotaSekolah' => $kuotaSekolah,
                'jurusanStats' => $jurusanStats,
                'isDaftarUlangOpen' => $isDaftarUlangOpen,
                'isVerifikasiOpen' => ScheduleService::isVerifikasiOpen(),
                'verifikasiStatus' => ScheduleService::getAgendaStatus('verifikasi'),
                'verifikasiErrorMessage' => ScheduleService::getAgendaErrorMessage('verifikasi'),
                'sudahDaftarUlangCount' => $sudahDaftarUlangCount,
                'belumDaftarUlangCount' => $belumDaftarUlangCount,
                'diverifikasiOlehOperatorCount' => $diverifikasiOlehOperatorCount,
            ]);
        }

        return view('livewire.operator.login');
    }
}
