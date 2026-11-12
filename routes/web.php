<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\RegistrationWizard;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/daftar', RegistrationWizard::class)->name('daftar');
