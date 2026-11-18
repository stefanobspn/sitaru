<?php

use App\Models\Sekolah;
use App\Models\User;

it('creates a sekolah using factory', function () {
    $sekolah = Sekolah::factory()->create([
        'npsn' => '20327988',
        'nama_sekolah' => 'SMA Negeri 1 Surakarta',
        'kuota' => 120,
    ]);

    expect($sekolah->exists)->toBeTrue()
        ->and($sekolah->npsn)->toBe('20327988')
        ->and($sekolah->nama_sekolah)->toBe('SMA Negeri 1 Surakarta')
        ->and($sekolah->nama)->toBe('SMA Negeri 1 Surakarta')
        ->and($sekolah->kuota)->toBe(120);
});

it('can associate an operator with a sekolah', function () {
    $sekolah = Sekolah::factory()->withOperator()->create();

    expect($sekolah->operator)->toBeInstanceOf(User::class)
        ->and($sekolah->operator->sekolah->id)->toBe($sekolah->id);
});
