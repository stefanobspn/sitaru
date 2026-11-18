<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Services\ScheduleService;
use App\Services\SeleksiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Siswa extends Component
{
    public string $nisn = '';

    public string $password = '';

    public bool $remember = false;

    public bool $isChoosingSchool = false;

    public ?int $pilihan_sekolah_1_id = null;

    public ?int $pilihan_jurusan_1_id = null;

    public ?int $pilihan_sekolah_2_id = null;

    public ?int $pilihan_jurusan_2_id = null;

    public function updatedPilihanSekolah1Id(): void
    {
        $this->pilihan_jurusan_1_id = null;

        if ($this->pilihan_sekolah_1_id && $this->pilihan_sekolah_2_id) {
            $sekolah1 = Sekolah::find($this->pilihan_sekolah_1_id);
            $sekolah2 = Sekolah::find($this->pilihan_sekolah_2_id);
            if ($sekolah1 && $sekolah2 && ($sekolah1->jenis_sekolah !== $sekolah2->jenis_sekolah || $sekolah1->id === $sekolah2->id)) {
                $this->pilihan_sekolah_2_id = null;
                $this->pilihan_jurusan_2_id = null;
            }
        }
    }

    public function updatedPilihanSekolah2Id(): void
    {
        $this->pilihan_jurusan_2_id = null;
    }

    public function login(): void
    {
        $this->validate([
            'nisn' => ['required', 'numeric', 'digits:10'],
            'password' => ['required'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits' => 'NISN harus berjumlah 10 digit.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt(['nisn' => $this->nisn, 'password' => $this->password, 'role' => UserRole::Siswa], $this->remember)) {
            session()->regenerate();
            $this->reset(['password']);

            return;
        }

        $this->addError('nisn', 'NISN atau password salah, atau akun belum diaktivasi.');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->reset(['nisn', 'password', 'remember', 'isChoosingSchool', 'pilihan_sekolah_1_id', 'pilihan_jurusan_1_id', 'pilihan_sekolah_2_id', 'pilihan_jurusan_2_id']);
    }

    public function cetakBuktiPendaftaran()
    {
        $siswa = Auth::user()?->registeredSiswa ?? RegisteredSiswa::where('nisn', Auth::user()?->nisn)->first();
        if (! $siswa || ! $siswa->nomor_pendaftaran) {
            return;
        }

        $pdf = Pdf::loadView('pdf.bukti-pendaftaran', [
            'siswa' => $siswa,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "bukti-pendaftaran-{$siswa->nomor_pendaftaran}.pdf");
    }

    public function bukaFormPilihanSekolah(): void
    {
        if (! ScheduleService::isPendaftaranSekolahOpen()) {
            session()->flash('error', ScheduleService::getAgendaErrorMessage('daftar_sekolah'));

            return;
        }

        $siswa = Auth::user()?->registeredSiswa ?? RegisteredSiswa::where('nisn', Auth::user()?->nisn)->first();
        if ($siswa && in_array($siswa->status_seleksi, ['diterima_pilihan_1', 'diterima_pilihan_2', 'tidak_lolos'], true)) {
            session()->flash('error', 'Pilihan sekolah tidak dapat diubah karena hasil seleksi sudah diumumkan/dikunci.');

            return;
        }

        if ($siswa) {
            $this->pilihan_sekolah_1_id = $siswa->pilihan_sekolah_1_id;
            $this->pilihan_jurusan_1_id = $siswa->pilihan_jurusan_1_id;
            $this->pilihan_sekolah_2_id = $siswa->pilihan_sekolah_2_id;
            $this->pilihan_jurusan_2_id = $siswa->pilihan_jurusan_2_id;
        }
        $this->isChoosingSchool = true;
        $this->resetValidation();
    }

    public function tutupFormPilihanSekolah(): void
    {
        $this->isChoosingSchool = false;
        $this->resetValidation();
    }

    public function simpanPilihanSekolah(): void
    {
        if (! ScheduleService::isPendaftaranSekolahOpen()) {
            session()->flash('error', ScheduleService::getAgendaErrorMessage('daftar_sekolah'));

            return;
        }

        $siswa = Auth::user()?->registeredSiswa ?? RegisteredSiswa::where('nisn', Auth::user()?->nisn)->first();
        if (! $siswa) {
            return;
        }

        if (in_array($siswa->status_seleksi, ['diterima_pilihan_1', 'diterima_pilihan_2', 'tidak_lolos'], true)) {
            session()->flash('error', 'Pilihan sekolah tidak dapat diubah karena hasil seleksi sudah diumumkan/dikunci.');

            return;
        }

        $sekolah1 = $this->pilihan_sekolah_1_id ? Sekolah::find($this->pilihan_sekolah_1_id) : null;
        $sekolah2 = $this->pilihan_sekolah_2_id ? Sekolah::find($this->pilihan_sekolah_2_id) : null;

        $rules = [
            'pilihan_sekolah_1_id' => ['required', 'exists:sekolahs,id'],
            'pilihan_sekolah_2_id' => [
                'nullable',
                'exists:sekolahs,id',
                'different:pilihan_sekolah_1_id',
                function ($attribute, $value, $fail) use ($sekolah1, $sekolah2) {
                    if ($sekolah1 && $sekolah2 && $sekolah1->jenis_sekolah !== $sekolah2->jenis_sekolah) {
                        $fail('Sekolah Pilihan 2 harus berjenis sama dengan Pilihan 1 (keduanya harus '.$sekolah1->jenis_sekolah.').');
                    }
                },
            ],
        ];

        $messages = [
            'pilihan_sekolah_1_id.required' => 'Sekolah Pilihan 1 wajib dipilih.',
            'pilihan_sekolah_1_id.exists' => 'Sekolah Pilihan 1 tidak valid.',
            'pilihan_sekolah_2_id.exists' => 'Sekolah Pilihan 2 tidak valid.',
            'pilihan_sekolah_2_id.different' => 'Sekolah Pilihan 2 tidak boleh sama dengan Sekolah Pilihan 1.',
        ];

        if ($sekolah1?->isSmk()) {
            $rules['pilihan_jurusan_1_id'] = [
                'required',
                Rule::exists('jurusans', 'id')->where('sekolah_id', $sekolah1->id),
            ];
            $messages['pilihan_jurusan_1_id.required'] = 'Jurusan untuk Pilihan 1 wajib dipilih.';
            $messages['pilihan_jurusan_1_id.exists'] = 'Jurusan untuk Pilihan 1 tidak valid.';
        }

        if ($sekolah2?->isSmk()) {
            $rules['pilihan_jurusan_2_id'] = [
                'required',
                Rule::exists('jurusans', 'id')->where('sekolah_id', $sekolah2->id),
            ];
            $messages['pilihan_jurusan_2_id.required'] = 'Jurusan untuk Pilihan 2 wajib dipilih.';
            $messages['pilihan_jurusan_2_id.exists'] = 'Jurusan untuk Pilihan 2 tidak valid.';
        }

        $this->validate($rules, $messages);

        $siswa->update([
            'pilihan_sekolah_1_id' => $this->pilihan_sekolah_1_id,
            'pilihan_jurusan_1_id' => $sekolah1?->isSmk() ? $this->pilihan_jurusan_1_id : null,
            'pilihan_sekolah_2_id' => $this->pilihan_sekolah_2_id ?: null,
            'pilihan_jurusan_2_id' => $sekolah2?->isSmk() ? $this->pilihan_jurusan_2_id : null,
            'status_seleksi' => 'menunggu',
            'tgl_pilih_sekolah' => now(),
        ]);

        $this->isChoosingSchool = false;
        session()->flash('success', 'Pilihan sekolah berhasil disimpan! Nilai rapor Anda akan diikutsertakan dalam seleksi.');
    }

    public function render(): View
    {
        if (Auth::check() && Auth::user()?->role === UserRole::Siswa) {
            $registeredSiswa = Auth::user()->registeredSiswa ?? RegisteredSiswa::where('nisn', Auth::user()->nisn)->first();
            $sekolahs = Sekolah::with('jurusans')->orderBy('nama_sekolah')->get();

            $statusSeleksi = null;
            if ($registeredSiswa && $registeredSiswa->pilihan_sekolah_1_id) {
                $penempatan = SeleksiService::hitungPenempatan();
                $statusSeleksi = $penempatan['status_by_siswa'][$registeredSiswa->id] ?? null;
            }

            $isPendaftaranSekolahOpen = ScheduleService::isPendaftaranSekolahOpen();
            $pendaftaranSekolahStatus = ScheduleService::getAgendaStatus('daftar_sekolah');
            $isHasilSeleksiOpen = ScheduleService::isHasilSeleksiOpen();
            $hasilSeleksiStatus = ScheduleService::getAgendaStatus('hasil_seleksi');
            $isDaftarUlangOpen = ScheduleService::isDaftarUlangOpen();
            $daftarUlangStatus = ScheduleService::getAgendaStatus('daftar_ulang');

            return view('livewire.siswa.dashboard', [
                'siswa' => $registeredSiswa,
                'sekolahs' => $sekolahs,
                'statusSeleksi' => $statusSeleksi,
                'isPendaftaranSekolahOpen' => $isPendaftaranSekolahOpen,
                'pendaftaranSekolahStatus' => $pendaftaranSekolahStatus,
                'isHasilSeleksiOpen' => $isHasilSeleksiOpen,
                'hasilSeleksiStatus' => $hasilSeleksiStatus,
                'isDaftarUlangOpen' => $isDaftarUlangOpen,
                'daftarUlangStatus' => $daftarUlangStatus,
            ]);
        }

        return view('livewire.siswa.login');
    }
}
