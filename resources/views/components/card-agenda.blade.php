@props([
    'agenda' => 'pendaftaran',
    'currentAgenda' => false,
])

@php
    $jadwalList = [
        'pendaftaran' => [
            'title' => 'Pendaftaran Akun',
            'date'  => '3 - 12 Juni',
            'path'  => 'M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4m-9-2V7H4v3H1v2h3v3h2v-3h3v-2zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4',
        ],
        'verifikasi' => [
            'title' => 'Verifikasi Akun',
            'date'  => '3 - 12 Juni',
            'path'  => 'M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4l1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z',
        ],
        'aktivasi' => [
            'title' => 'Aktivasi Akun',
            'date'  => '4 - 13 Juni',
            'path'  => 'M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2s2 .9 2 2s-.9 2-2 2z',
        ],
        'daftar_sekolah' => [
            'title' => 'Daftar Sekolah',
            'date'  => '15 - 18 Juni',
            'path'  => 'M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6l9-4.91V17h2V9L12 3z',
        ],
        'hasil_seleksi' => [
            'title' => 'Hasil Seleksi SPMB',
            'date'  => '19 - 21 Juni',
            'path'  => 'M18 11v2h4v-2h-4zm-2 6.61c.96.71 2.21 1.65 3.2 2.39c.4-.53.8-1.07 1.2-1.6c-.99-.74-2.24-1.68-3.2-2.4c-.4.54-.8 1.08-1.2 1.61zM20.4 5.6c-.4-.53-.8-1.07-1.2-1.6c-.99.74-2.24 1.68-3.2 2.4c.4.53.8 1.07 1.2 1.6c.96-.72 2.21-1.65 3.2-2.4zM4 9c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2h1v4h2v-4h1l5 3V6L8 9H4zm11.5 3c0-1.33-.58-2.53-1.5-3.35v6.69c.92-.81 1.5-2.01 1.5-3.34z',
        ],
        'daftar_ulang' => [
            'title' => 'Daftar Ulang',
            'date'  => '26 Juni',
            'path'  => 'M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1zm0 4c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm6 12H6v-1.4c0-2 4-3.1 6-3.1s6 1.1 6 3.1V19z',
        ],
    ];

    $current = $jadwalList[$agenda] ?? $jadwalList['pendaftaran'];
@endphp

@if ($currentAgenda)
    {{-- Tampilan Aura Card saat jadwal sedang aktif --}}
    <div {{ $attributes->merge(['class' => 'aura text-blue-500']) }}>
        <div class="card bg-base-100 text-base-content h-48 md:h-64 rounded-lg shadow-sm">
            <div class="card-body flex flex-col items-center justify-center gap-2 p-5 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-1 text-blue-500" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path fill="currentColor" d="{{ $current['path'] }}" />
                </svg>
                <span class="text-lg font-semibold tracking-wide">{{ $current['title'] }}</span>
                <span class="text-sm font-medium opacity-80">{{ $current['date'] }}</span>
            </div>
        </div>
    </div>
@else
    {{-- Tampilan Default --}}
    <div {{ $attributes->merge(['class' => 'bg-[#7396B3] h-48 md:h-64 rounded-lg flex flex-col items-center justify-center gap-2 p-5 text-white text-center shadow-sm']) }}>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-1" viewBox="0 0 24 24">
            <path d="M0 0h24v24H0z" fill="none" />
            <path fill="currentColor" d="{{ $current['path'] }}" />
        </svg>
        <span class="text-lg font-semibold tracking-wide">{{ $current['title'] }}</span>
        <span class="text-sm font-medium text-white/90">{{ $current['date'] }}</span>
    </div>
@endif
