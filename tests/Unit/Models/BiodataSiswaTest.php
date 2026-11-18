<?php

use App\Models\BiodataSiswa;
use Carbon\CarbonInterface;

it('creates a biodata siswa using factory', function () {
    $biodata = BiodataSiswa::factory()->create([
        'nama_lengkap' => 'Ahmad Dahlan',
    ]);

    expect($biodata->exists)->toBeTrue()
        ->and($biodata->nama_lengkap)->toBe('Ahmad Dahlan');
});

it('casts tanggal_lahir to date', function () {
    $biodata = BiodataSiswa::factory()->create([
        'tanggal_lahir' => '2008-01-15',
    ]);

    expect($biodata->tanggal_lahir)->toBeInstanceOf(CarbonInterface::class)
        ->and($biodata->tanggal_lahir->format('Y-m-d'))->toBe('2008-01-15');
});
