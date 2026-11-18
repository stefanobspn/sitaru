<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\App;

class ScheduleService
{
    public const JADWAL_PENDAFTARAN_START = '2026-06-15 00:00:00';

    public const JADWAL_PENDAFTARAN_END = '2026-06-18 23:59:59';

    public const JADWAL_DAFTAR_ULANG_START = '2026-06-26 00:00:00';

    /**
     * Mengambil waktu acuan saat ini (mendukung Carbon::setTestNow, override via query param ?simulated_date, session, dan konfigurasi .env).
     */
    public static function now(): CarbonInterface
    {
        if (Carbon::hasTestNow()) {
            return Carbon::getTestNow();
        }

        if (App::bound('request')) {
            $request = request();
            if ($request && $request->has('simulated_date')) {
                $param = $request->query('simulated_date');
                if (in_array($param, ['reset', 'clear', 'off'], true)) {
                    if (App::bound('session')) {
                        session()->forget('spmb_simulated_date');
                    }
                } elseif ($param) {
                    try {
                        $parsed = Carbon::parse($param);
                        if (App::bound('session')) {
                            session(['spmb_simulated_date' => $parsed->format('Y-m-d H:i:s')]);
                        }

                        return $parsed;
                    } catch (\Throwable) {
                        // ignore invalid input
                    }
                }
            }
        }

        if (App::bound('session') && session()->has('spmb_simulated_date')) {
            try {
                return Carbon::parse(session('spmb_simulated_date'));
            } catch (\Throwable) {
                session()->forget('spmb_simulated_date');
            }
        }

        $configDate = config('spmb.simulated_date', env('SPMB_SIMULATED_DATE', '2026-06-05 08:00:00'));
        if (! empty($configDate)) {
            try {
                return Carbon::parse($configDate);
            } catch (\Throwable) {
                // ignore
            }
        }

        return Carbon::now();
    }

    /**
     * Cek apakah tahap pemilihan sekolah sedang dibuka (15 - 18 Juni 2026).
     */
    public static function isPendaftaranSekolahOpen(): bool
    {
        return self::isAgendaActive('daftar_sekolah');
    }

    /**
     * Cek apakah tahap pengajuan akun sedang dibuka (3 - 12 Juni 2026).
     */
    public static function isPengajuanAkunOpen(): bool
    {
        return self::isAgendaActive('pendaftaran');
    }

    /**
     * Cek apakah tahap verifikasi berkas sedang dibuka (3 - 12 Juni 2026).
     */
    public static function isVerifikasiOpen(): bool
    {
        return self::isAgendaActive('verifikasi');
    }

    /**
     * Cek apakah tahap aktivasi akun sedang dibuka (4 - 13 Juni 2026).
     */
    public static function isAktivasiAkunOpen(): bool
    {
        return self::isAgendaActive('aktivasi');
    }

    /**
     * Cek apakah tahap hasil seleksi sudah dibuka (>= 19 Juni 2026).
     */
    public static function isHasilSeleksiOpen(): bool
    {
        return self::now()->gte(Carbon::parse(self::AGENDAS['hasil_seleksi']['start']));
    }

    /**
     * Cek apakah tahap daftar ulang di operator sudah dibuka (>= 26 Juni 2026).
     */
    public static function isDaftarUlangOpen(): bool
    {
        return self::now()->gte(Carbon::parse(self::JADWAL_DAFTAR_ULANG_START));
    }

    public const AGENDAS = [
        'pendaftaran' => [
            'start' => '2026-06-03 00:00:00',
            'end' => '2026-06-12 23:59:59',
        ],
        'verifikasi' => [
            'start' => '2026-06-03 00:00:00',
            'end' => '2026-06-12 23:59:59',
        ],
        'aktivasi' => [
            'start' => '2026-06-04 00:00:00',
            'end' => '2026-06-13 23:59:59',
        ],
        'daftar_sekolah' => [
            'start' => '2026-06-15 00:00:00',
            'end' => '2026-06-18 23:59:59',
        ],
        'hasil_seleksi' => [
            'start' => '2026-06-19 00:00:00',
            'end' => '2026-06-21 23:59:59',
        ],
        'daftar_ulang' => [
            'start' => '2026-06-26 00:00:00',
            'end' => '2026-06-26 23:59:59',
        ],
    ];

    /**
     * Cek apakah agenda tertentu sedang berjalan pada waktu acuan saat ini.
     * Mendukung multi-agenda yang berjalan bersamaan (misal 5 Juni: pendaftaran, verifikasi, aktivasi).
     */
    public static function isAgendaActive(string $agenda): bool
    {
        if (! isset(self::AGENDAS[$agenda])) {
            return false;
        }

        $current = self::now();
        $start = Carbon::parse(self::AGENDAS[$agenda]['start']);
        $end = Carbon::parse(self::AGENDAS[$agenda]['end']);

        if ($agenda === 'daftar_ulang') {
            return $current->gte($start);
        }

        return $current->betweenIncluded($start, $end);
    }

    /**
     * Cek status tahapan agenda: 'not_started' | 'active' | 'closed'
     */
    public static function getAgendaStatus(string $agenda): string
    {
        if (! isset(self::AGENDAS[$agenda])) {
            return 'closed';
        }

        $current = self::now();
        $start = Carbon::parse(self::AGENDAS[$agenda]['start']);
        $end = Carbon::parse(self::AGENDAS[$agenda]['end']);

        if ($current->lt($start)) {
            return 'not_started';
        }

        if ($agenda === 'daftar_ulang') {
            return $current->gte($start) ? 'active' : 'not_started';
        }

        if ($current->gt($end)) {
            return 'closed';
        }

        return 'active';
    }

    /**
     * Pesan kesalahan informatif ketika fitur diakses di luar jadwal resminya.
     */
    public static function getAgendaErrorMessage(string $agenda): string
    {
        $status = self::getAgendaStatus($agenda);

        return match ($agenda) {
            'pendaftaran' => match ($status) {
                'not_started' => 'Tahap pengajuan akun belum dibuka. Jadwal pendaftaran dibuka pada tanggal 3 - 12 Juni 2026.',
                'closed' => 'Tahap pengajuan akun calon siswa telah ditutup pada tanggal 12 Juni 2026.',
                default => '',
            },
            'verifikasi' => match ($status) {
                'not_started' => 'Tahap verifikasi berkas pendaftaran belum dibuka. Jadwal verifikasi pada tanggal 3 - 12 Juni 2026.',
                'closed' => 'Tahap verifikasi berkas pendaftaran telah ditutup pada tanggal 12 Juni 2026.',
                default => '',
            },
            'aktivasi' => match ($status) {
                'not_started' => 'Tahap aktivasi akun siswa belum dibuka. Jadwal aktivasi akun pada tanggal 4 - 13 Juni 2026.',
                'closed' => 'Tahap aktivasi akun siswa telah ditutup pada tanggal 13 Juni 2026.',
                default => '',
            },
            'daftar_sekolah' => match ($status) {
                'not_started' => 'Pendaftaran dan pemilihan sekolah belum dapat dilakukan sebelum jadwal resminya (15 Juni 2026).',
                'closed' => 'Pendaftaran dan pemilihan sekolah tujuan telah ditutup pada tanggal 18 Juni 2026 pukul 23:59 WIB.',
                default => '',
            },
            'daftar_ulang' => match ($status) {
                'not_started' => 'Konfirmasi daftar ulang belum dapat dilakukan sebelum jadwal resminya (26 Juni 2026).',
                default => '',
            },
            default => 'Jadwal tahapan belum dimulai atau telah berakhir.',
        };
    }

    /**
     * Mendapatkan daftar semua key agenda yang sedang berjalan bersamaan pada waktu acuan saat ini.
     *
     * @return array<string>
     */
    public static function getActiveAgendas(): array
    {
        $active = [];
        foreach (array_keys(self::AGENDAS) as $agenda) {
            if (self::isAgendaActive($agenda)) {
                $active[] = $agenda;
            }
        }

        return $active;
    }

    /**
     * Mendapatkan key agenda pertama yang aktif (atau null jika tidak ada).
     */
    public static function getActiveAgenda(): ?string
    {
        $agendas = self::getActiveAgendas();

        return $agendas[0] ?? null;
    }
}
