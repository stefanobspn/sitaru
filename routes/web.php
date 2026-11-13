<?php

use App\Livewire\Admin;
use App\Livewire\Operator;
use Illuminate\Support\Facades\Route;
use App\Livewire\RegistrationWizard;

Route::get('/', function () {
    return view('index');
});

Route::livewire('/daftar', RegistrationWizard::class)->name('daftar');
Route::get('/daftar/cetak-bukti/{nomor}', function (string $nomor) {
    $siswa = \App\Models\RegisteredSiswa::where('nomor_pendaftaran', $nomor)->firstOrFail();
    return response("Bukti Pendaftaran: {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran})");
})->name('pendaftaran.cetak-bukti');
Route::livewire('/admin', Admin::class)->name('admin');
Route::livewire('/operator', Operator::class)->name('operator');
