<?php

use App\Services\ScheduleService;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow();
    if (session()->isStarted()) {
        session()->forget('spmb_simulated_date');
    }
});

afterEach(function () {
    Carbon::setTestNow();
    if (session()->isStarted()) {
        session()->forget('spmb_simulated_date');
    }
});

it('prioritizes Carbon::setTestNow when present', function () {
    Carbon::setTestNow('2026-06-16 12:00:00');

    expect(ScheduleService::now()->format('Y-m-d H:i:s'))->toBe('2026-06-16 12:00:00')
        ->and(ScheduleService::isPendaftaranSekolahOpen())->toBeTrue()
        ->and(ScheduleService::isDaftarUlangOpen())->toBeFalse()
        ->and(ScheduleService::getActiveAgenda())->toBe('daftar_sekolah')
        ->and(ScheduleService::isAgendaActive('daftar_sekolah'))->toBeTrue()
        ->and(ScheduleService::isAgendaActive('pendaftaran'))->toBeFalse();
});

it('highlights multiple concurrent active agendas on 5 June 2026', function () {
    Carbon::setTestNow('2026-06-05 08:00:00');

    // Pada 5 Juni, pendaftaran, verifikasi, dan aktivasi berjalan bersamaan
    expect(ScheduleService::isAgendaActive('pendaftaran'))->toBeTrue()
        ->and(ScheduleService::isAgendaActive('verifikasi'))->toBeTrue()
        ->and(ScheduleService::isAgendaActive('aktivasi'))->toBeTrue()
        ->and(ScheduleService::isAgendaActive('daftar_sekolah'))->toBeFalse()
        ->and(ScheduleService::isAgendaActive('hasil_seleksi'))->toBeFalse()
        ->and(ScheduleService::isAgendaActive('daftar_ulang'))->toBeFalse()
        ->and(ScheduleService::getActiveAgendas())->toBe(['pendaftaran', 'verifikasi', 'aktivasi']);
});

it('correctly maps agenda stages according to individual date ranges', function () {
    // 1. Pada 13 Juni (pendaftaran & verifikasi berakhir, aktivasi hari terakhir)
    Carbon::setTestNow('2026-06-13 10:00:00');
    expect(ScheduleService::isAgendaActive('pendaftaran'))->toBeFalse()
        ->and(ScheduleService::isAgendaActive('verifikasi'))->toBeFalse()
        ->and(ScheduleService::isAgendaActive('aktivasi'))->toBeTrue()
        ->and(ScheduleService::isPendaftaranSekolahOpen())->toBeFalse();

    // 2. Pada 15 - 18 Juni (Daftar Sekolah aktif)
    Carbon::setTestNow('2026-06-15 00:00:00');
    expect(ScheduleService::isAgendaActive('daftar_sekolah'))->toBeTrue()
        ->and(ScheduleService::isAgendaActive('pendaftaran'))->toBeFalse()
        ->and(ScheduleService::isPendaftaranSekolahOpen())->toBeTrue()
        ->and(ScheduleService::isDaftarUlangOpen())->toBeFalse();

    // 3. Pada 19 - 21 Juni (Hasil Seleksi aktif)
    Carbon::setTestNow('2026-06-19 09:00:00');
    expect(ScheduleService::isAgendaActive('hasil_seleksi'))->toBeTrue()
        ->and(ScheduleService::isAgendaActive('daftar_sekolah'))->toBeFalse();

    // 4. Pada 26 Juni (Daftar Ulang aktif)
    Carbon::setTestNow('2026-06-26 08:00:00');
    expect(ScheduleService::isAgendaActive('daftar_ulang'))->toBeTrue()
        ->and(ScheduleService::isDaftarUlangOpen())->toBeTrue();
});

it('uses simulated_date from session when set', function () {
    session(['spmb_simulated_date' => '2026-06-17 14:00:00']);

    expect(ScheduleService::now()->format('Y-m-d H:i:s'))->toBe('2026-06-17 14:00:00')
        ->and(ScheduleService::isPendaftaranSekolahOpen())->toBeTrue()
        ->and(ScheduleService::isAgendaActive('daftar_sekolah'))->toBeTrue();
});

it('correctly reports agenda statuses and closes pendaftaran and aktivasi on 15 June 2026', function () {
    Carbon::setTestNow('2026-06-15 10:00:00');

    expect(ScheduleService::getAgendaStatus('pendaftaran'))->toBe('closed')
        ->and(ScheduleService::isPengajuanAkunOpen())->toBeFalse()
        ->and(ScheduleService::getAgendaStatus('verifikasi'))->toBe('closed')
        ->and(ScheduleService::isVerifikasiOpen())->toBeFalse()
        ->and(ScheduleService::getAgendaStatus('aktivasi'))->toBe('closed')
        ->and(ScheduleService::isAktivasiAkunOpen())->toBeFalse()
        ->and(ScheduleService::getAgendaStatus('daftar_sekolah'))->toBe('active')
        ->and(ScheduleService::isPendaftaranSekolahOpen())->toBeTrue()
        ->and(ScheduleService::getAgendaStatus('hasil_seleksi'))->toBe('not_started')
        ->and(ScheduleService::getAgendaErrorMessage('pendaftaran'))->toContain('telah ditutup')
        ->and(ScheduleService::getAgendaErrorMessage('aktivasi'))->toContain('telah ditutup');
});

it('closes daftar_sekolah on 19 June 2026 during hasil_seleksi', function () {
    Carbon::setTestNow('2026-06-19 08:00:00');

    expect(ScheduleService::getAgendaStatus('daftar_sekolah'))->toBe('closed')
        ->and(ScheduleService::isPendaftaranSekolahOpen())->toBeFalse()
        ->and(ScheduleService::getAgendaStatus('hasil_seleksi'))->toBe('active')
        ->and(ScheduleService::isHasilSeleksiOpen())->toBeTrue()
        ->and(ScheduleService::getAgendaErrorMessage('daftar_sekolah'))->toContain('telah ditutup');
});
