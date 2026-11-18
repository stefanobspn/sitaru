<?php

use App\Livewire\Admin;
use App\Livewire\AktivasiAkun;
use App\Livewire\Operator;
use App\Livewire\RegistrationWizard;
use App\Livewire\Seleksi;
use App\Livewire\Siswa;
use App\Models\RegisteredSiswa;
use App\Models\Sekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index', [
        'totalPendaftar' => RegisteredSiswa::count(),
        'pendaftarHariIni' => RegisteredSiswa::whereDate('created_at', today())->count(),
        'totalKuota' => (int) Sekolah::sum('kuota'),
    ]);
})->name('index');

Route::livewire('/daftar', RegistrationWizard::class)->name('daftar');
Route::get('/daftar/cetak-bukti/{nomor}', function (string $nomor) {
    $siswa = RegisteredSiswa::with(['biodataSiswa', 'pilihanSekolah1', 'pilihanSekolah2', 'pilihanJurusan1', 'pilihanJurusan2', 'diterimaSekolah'])
        ->where('nomor_pendaftaran', $nomor)
        ->firstOrFail();

    $pdf = Pdf::loadView('pdf.bukti-pendaftaran', [
        'siswa' => $siswa,
    ])->setPaper('a4', 'portrait');

    return $pdf->stream("bukti-pendaftaran-{$siswa->nomor_pendaftaran}.pdf");
})->name('pendaftaran.cetak-bukti');

Route::livewire('/aktivasi', AktivasiAkun::class)->name('aktivasi');
Route::livewire('/siswa', Siswa::class)->name('siswa');
Route::livewire('/seleksi', Seleksi::class)->name('seleksi');

Route::livewire('/admin', Admin::class)->name('admin');
Route::livewire('/operator', Operator::class)->name('operator');
