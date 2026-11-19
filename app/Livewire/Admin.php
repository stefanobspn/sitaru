<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use App\Models\User;
use App\Services\SeleksiService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;

class Admin extends Component
{
    #[Url(as: 'menu')]
    public string $activeMenu = 'beranda';

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public bool $isCreatingSchool = false;

    public bool $isEditingSchool = false;

    public ?int $editingSchoolId = null;

    public string $npsn_sekolah = '';

    public string $nama_sekolah = '';

    public string $jenis_sekolah = 'SMA';

    public array $jurusans = [];

    public string $nip_operator = '';

    public string $password_operator = '';

    public string $kuota_awal = '50';

    public function setMenu(string $menu): void
    {
        $this->activeMenu = $menu;
        $this->isCreatingSchool = false;
        $this->isEditingSchool = false;
    }

    public function updatedJenisSekolah(string $value): void
    {
        if ($value === 'SMK' && count($this->jurusans) < 2) {
            $this->jurusans = ['', ''];
        } elseif ($value === 'SMA') {
            $this->jurusans = [];
        }
    }

    public function addJurusan(): void
    {
        $this->jurusans[] = '';
    }

    public function removeJurusan(int $index): void
    {
        unset($this->jurusans[$index]);
        $this->jurusans = array_values($this->jurusans);
    }

    public function openCreateSchoolForm(): void
    {
        $this->authorizeAdmin();
        $this->isCreatingSchool = true;
        $this->isEditingSchool = false;
    }

    public function editSchool(int $schoolId): void
    {
        $this->authorizeAdmin();

        $sekolah = Sekolah::with('jurusans')->findOrFail($schoolId);
        $this->editingSchoolId = $sekolah->id;
        $this->npsn_sekolah = $sekolah->npsn;
        $this->nama_sekolah = $sekolah->nama_sekolah;
        $this->jenis_sekolah = $sekolah->jenis_sekolah;
        $this->nip_operator = $sekolah->operator?->nip ?? '';
        $this->password_operator = '';
        $this->kuota_awal = (string) $sekolah->kuota;
        $this->jurusans = $sekolah->jurusans->pluck('nama_jurusan')->all();
        $this->isCreatingSchool = false;
        $this->isEditingSchool = true;
        $this->activeMenu = 'manage';
        $this->resetValidation();
    }

    public function updateSchool(): void
    {
        $this->authorizeAdmin();

        $sekolah = Sekolah::with('operator')->findOrFail($this->editingSchoolId);
        $operator = $sekolah->operator;
        $rules = [
            'npsn_sekolah' => ['required', 'numeric', 'digits_between:6,10', Rule::unique('sekolahs', 'npsn')->ignore($sekolah->id)],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'jenis_sekolah' => ['required', 'in:SMA,SMK'],
            'nip_operator' => ['required', 'numeric', 'digits_between:8,20', Rule::unique('users', 'nip')->ignore($operator?->id)],
            'password_operator' => ['nullable', 'string', 'min:4'],
            'kuota_awal' => ['required', 'integer', 'min:1'],
        ];

        if ($this->jenis_sekolah === 'SMK') {
            $rules['jurusans'] = ['required', 'array', 'min:2'];
            $rules['jurusans.*'] = ['required', 'string', 'max:255', 'distinct'];
        }

        $validated = $this->validate($rules);

        DB::transaction(function () use ($sekolah, $operator, $validated): void {
            $sekolah->update([
                'npsn' => $validated['npsn_sekolah'],
                'nama_sekolah' => $validated['nama_sekolah'],
                'jenis_sekolah' => $validated['jenis_sekolah'],
                'kuota' => (int) $validated['kuota_awal'],
            ]);

            if ($operator !== null) {
                $operatorAttributes = ['nip' => $validated['nip_operator']];
                if (filled($validated['password_operator'])) {
                    $operatorAttributes['password'] = $validated['password_operator'];
                }
                $operator->update($operatorAttributes);
            } else {
                $operator = User::create([
                    'nip' => $validated['nip_operator'],
                    'password' => filled($validated['password_operator']) ? $validated['password_operator'] : 'password',
                    'role' => UserRole::Operator,
                ]);
                $sekolah->operator()->associate($operator)->save();
            }

            if ($validated['jenis_sekolah'] === 'SMK') {
                $sekolah->jurusans()->whereNotIn('nama_jurusan', $validated['jurusans'])->delete();
                $existingJurusans = $sekolah->jurusans()->pluck('nama_jurusan')->all();
                foreach ($validated['jurusans'] as $namaJurusan) {
                    $namaJurusan = trim($namaJurusan);
                    if (! in_array($namaJurusan, $existingJurusans, true)) {
                        $sekolah->jurusans()->create(['nama_jurusan' => $namaJurusan]);
                    }
                }
            } else {
                $sekolah->jurusans()->delete();
            }
        });

        session()->flash('success', 'Data sekolah berhasil diperbarui.');
        $this->closeCreateSchoolForm();
    }

    public function deleteSchool(int $schoolId): void
    {
        $this->authorizeAdmin();

        $sekolah = Sekolah::with('operator')->findOrFail($schoolId);
        $isUsed = RegisteredSiswa::query()
            ->where(function ($query) use ($schoolId): void {
                $query->where('pilihan_sekolah_1_id', $schoolId)
                    ->orWhere('pilihan_sekolah_2_id', $schoolId)
                    ->orWhere('diterima_sekolah_id', $schoolId);
            })
            ->exists();

        if ($isUsed) {
            session()->flash('error', 'Sekolah tidak dapat dihapus karena masih digunakan oleh data pendaftar.');

            return;
        }

        DB::transaction(function () use ($sekolah): void {
            $operator = $sekolah->operator;
            $sekolah->jurusans()->delete();
            $sekolah->delete();
            $operator?->delete();
        });

        session()->flash('success', 'Sekolah berhasil dihapus.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()?->role === UserRole::Admin, 403);
    }

    public function closeCreateSchoolForm(): void
    {
        $this->reset(['npsn_sekolah', 'nama_sekolah', 'nip_operator', 'password_operator', 'jurusans', 'editingSchoolId']);
        $this->jenis_sekolah = 'SMA';
        $this->resetValidation();
        $this->kuota_awal = '50';
        $this->isCreatingSchool = false;
        $this->isEditingSchool = false;
    }

    public function createSchool(): void
    {
        $rules = [
            'npsn_sekolah' => ['required', 'numeric', 'digits_between:6,10', 'unique:sekolahs,npsn'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'jenis_sekolah' => ['required', 'in:SMA,SMK'],
            'nip_operator' => ['required', 'numeric', 'digits_between:8,20', 'unique:users,nip'],
            'password_operator' => ['nullable', 'string', 'min:4'],
            'kuota_awal' => ['nullable', 'integer', 'min:1'],
        ];

        $messages = [
            'npsn_sekolah.required' => 'NPSN sekolah wajib diisi.',
            'npsn_sekolah.numeric' => 'NPSN sekolah harus berupa angka.',
            'npsn_sekolah.digits_between' => 'NPSN sekolah harus antara :min dan :max digit.',
            'npsn_sekolah.unique' => 'NPSN sekolah sudah terdaftar.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'jenis_sekolah.required' => 'Jenis sekolah wajib dipilih.',
            'jenis_sekolah.in' => 'Jenis sekolah harus SMA atau SMK.',
            'nip_operator.required' => 'NIP operator wajib diisi.',
            'nip_operator.numeric' => 'NIP operator harus berupa angka.',
            'nip_operator.digits_between' => 'NIP operator harus antara :min dan :max digit.',
            'nip_operator.unique' => 'NIP operator sudah terdaftar.',
            'kuota_awal.integer' => 'Kuota awal harus berupa angka.',
            'kuota_awal.min' => 'Kuota awal minimal 1.',
        ];

        if ($this->jenis_sekolah === 'SMK') {
            $rules['jurusans'] = ['required', 'array', 'min:2'];
            $rules['jurusans.*'] = ['required', 'string', 'max:255', 'distinct'];
            $messages['jurusans.required'] = 'Daftar jurusan wajib diisi untuk SMK.';
            $messages['jurusans.min'] = 'SMK wajib memiliki minimal lebih dari 1 jurusan (minimal 2 jurusan).';
            $messages['jurusans.*.required'] = 'Nama jurusan wajib diisi.';
            $messages['jurusans.*.distinct'] = 'Nama jurusan tidak boleh sama.';
        }

        $this->validate($rules, $messages);

        DB::transaction(function () {
            $operator = User::create([
                'nip' => $this->nip_operator,
                'password' => $this->password_operator ?: 'password',
                'role' => UserRole::Operator,
            ]);

            $sekolah = Sekolah::create([
                'npsn' => $this->npsn_sekolah,
                'nama_sekolah' => $this->nama_sekolah,
                'jenis_sekolah' => $this->jenis_sekolah,
                'operator_id' => $operator->id,
                'kuota' => (int) ($this->kuota_awal ?: 50),
            ]);

            if ($this->jenis_sekolah === 'SMK') {
                foreach ($this->jurusans as $namaJurusan) {
                    $sekolah->jurusans()->create([
                        'nama_jurusan' => trim($namaJurusan),
                    ]);
                }
            }
        });

        session()->flash('success', 'Sekolah berhasil ditambahkan.');
        $this->closeCreateSchoolForm();
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'role' => UserRole::Admin], $this->remember)) {
            session()->regenerate();
            $this->reset(['password']);

            return;
        }

        $this->addError('email', 'Email atau password salah, atau Anda bukan Admin.');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->reset(['email', 'password', 'remember', 'isCreatingSchool', 'npsn_sekolah', 'nama_sekolah', 'nip_operator', 'password_operator']);
        $this->kuota_awal = '50';
    }

    public function finalisasiHasilSeleksi(): void
    {
        SeleksiService::finalisasi();

        session()->flash('success', 'Hasil seleksi berhasil difinalisasi dan diumumkan kepada seluruh calon siswa.');
    }

    public function batalkanFinalisasi(): void
    {
        SeleksiService::batalkanFinalisasi();

        session()->flash('success', 'Status finalisasi seleksi berhasil dibatalkan. Pendaftaran dibuka kembali.');
    }

    public function render(): View
    {
        if (Auth::check() && Auth::user()?->role === UserRole::Admin) {
            $isFinalized = RegisteredSiswa::whereIn('status_seleksi', ['diterima_pilihan_1', 'diterima_pilihan_2', 'tidak_lolos'])->exists();
            $totalSiswa = RegisteredSiswa::count();
            $totalMemilihSekolah = RegisteredSiswa::whereNotNull('pilihan_sekolah_1_id')->count();
            $totalDiterima = RegisteredSiswa::whereNotNull('diterima_sekolah_id')->count();
            $totalTidakLolos = RegisteredSiswa::where('status_seleksi', 'tidak_lolos')->count();
            $totalKuota = (int) Sekolah::sum('kuota');
            $totalTerverifikasi = RegisteredSiswa::where('status_verifikasi', 'terverifikasi')->count();
            $totalMenungguVerifikasi = RegisteredSiswa::where('status_verifikasi', 'menunggu')->count();
            $totalDitolak = RegisteredSiswa::where('status_verifikasi', 'ditolak')->count();
            $totalAkunDiaktivasi = RegisteredSiswa::where('is_activated', true)->count();
            $totalSma = Sekolah::where('jenis_sekolah', 'SMA')->count();
            $totalSmk = Sekolah::where('jenis_sekolah', 'SMK')->count();
            $totalSudahDaftarUlang = RegisteredSiswa::where('is_daftar_ulang', true)->count();

            return view('livewire.admin.dashboard', [
                'sekolahs' => Sekolah::with(['operator', 'jurusans'])->latest()->get(),
                'isFinalized' => $isFinalized,
                'totalSiswa' => $totalSiswa,
                'totalMemilihSekolah' => $totalMemilihSekolah,
                'totalDiterima' => $totalDiterima,
                'totalTidakLolos' => $totalTidakLolos,
                'totalKuota' => $totalKuota,
                'totalTerverifikasi' => $totalTerverifikasi,
                'totalMenungguVerifikasi' => $totalMenungguVerifikasi,
                'totalDitolak' => $totalDitolak,
                'totalAkunDiaktivasi' => $totalAkunDiaktivasi,
                'totalSma' => $totalSma,
                'totalSmk' => $totalSmk,
                'totalSudahDaftarUlang' => $totalSudahDaftarUlang,
            ]);
        }

        return view('livewire.admin.login');
    }
}
