<?php

use App\Enums\StatusDomisili;
use App\Enums\StatusPrestasi;
use App\Enums\UserRole;

it('returns array of scalar values for enums using HasValues trait', function () {
    expect(UserRole::values())->toBe(['admin', 'operator', 'siswa'])
        ->and(StatusPrestasi::values())->toBe([
            StatusPrestasi::TidakMemiliki->value,
            StatusPrestasi::Memiliki->value,
        ])
        ->and(StatusDomisili::values())->toContain(
            StatusDomisili::SesuaiKK->value,
            StatusDomisili::SesuaiDomisiliPantiAsuhan->value
        );
});
