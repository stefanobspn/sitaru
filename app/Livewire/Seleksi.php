<?php

namespace App\Livewire;

use App\Models\Sekolah;
use App\Services\SeleksiService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Seleksi extends Component
{
    #[Url(as: 'sekolah')]
    public ?int $selectedSekolahId = null;

    #[Url(as: 'jurusan')]
    public ?int $selectedJurusanId = null;

    public string $search = '';

    public function updatedSelectedSekolahId(): void
    {
        $this->selectedJurusanId = null;
    }

    public function mount(): void
    {
        if (! $this->selectedSekolahId) {
            $this->selectedSekolahId = Sekolah::orderBy('nama_sekolah')->first()?->id;
        }
    }

    public function render(): View
    {
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        $selectedSekolah = $this->selectedSekolahId ? Sekolah::find($this->selectedSekolahId) : $sekolahs->first();
        $jurusans = ($selectedSekolah && $selectedSekolah->isSmk()) ? $selectedSekolah->jurusans()->orderBy('nama_jurusan')->get() : collect();

        $pendaftarList = collect();
        $totalPendaftar = 0;

        if ($selectedSekolah) {
            $penempatan = SeleksiService::hitungPenempatan();
            $pendaftarLolos = $penempatan['admitted_by_school'][$selectedSekolah->id] ?? collect();
            $totalPendaftar = $penempatan['total_pendaftar_by_school'][$selectedSekolah->id] ?? 0;

            if ($selectedSekolah->isSmk() && $this->selectedJurusanId) {
                $jurusanId = (int) $this->selectedJurusanId;
                $pendaftarLolos = $pendaftarLolos->filter(function ($siswa) use ($jurusanId, $selectedSekolah) {
                    $jurusanSiswaId = $siswa->jurusan_terpilih?->id
                        ?? ($siswa->pilihan_sekolah_1_id === $selectedSekolah->id
                            ? $siswa->pilihan_jurusan_1_id
                            : $siswa->pilihan_jurusan_2_id);

                    return (int) $jurusanSiswaId === $jurusanId;
                });
            }

            if (trim($this->search) !== '') {
                $searchLower = strtolower(trim($this->search));
                $pendaftarLolos = $pendaftarLolos->filter(function ($siswa) use ($searchLower) {
                    return str_contains(strtolower($siswa->nama_lengkap), $searchLower)
                        || str_contains(strtolower($siswa->nisn), $searchLower)
                        || str_contains(strtolower($siswa->nomor_pendaftaran), $searchLower);
                });
            }

            $pendaftarList = $pendaftarLolos;
        }

        return view('livewire.seleksi', [
            'sekolahs' => $sekolahs,
            'selectedSekolah' => $selectedSekolah,
            'jurusans' => $jurusans,
            'pendaftarList' => $pendaftarList,
            'totalPendaftar' => $totalPendaftar,
        ]);
    }
}
