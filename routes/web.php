<?php

use App\Livewire\Admin;
use App\Livewire\Operator;
use App\Livewire\RegistrationWizard;
use App\Models\RegisteredSiswa;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::livewire('/daftar', RegistrationWizard::class)->name('daftar');
Route::get('/daftar/cetak-bukti/{nomor}', function (string $nomor) {
    $siswa = RegisteredSiswa::where('nomor_pendaftaran', $nomor)->firstOrFail();

    return response("Bukti Pendaftaran: {$siswa->nama_lengkap} ({$siswa->nomor_pendaftaran})");
})->name('pendaftaran.cetak-bukti');

Route::livewire('/admin', Admin::class)->name('admin');
Route::livewire('/operator', Operator::class)->name('operator');
