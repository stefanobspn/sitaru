@props(['sekolahs' => []])

<div class="overflow-x-auto">
    <table class="table table-zebra">
        <!-- head -->
        <thead>
        <tr>
            <th></th>
            <th class="text-center">Nama Sekolah</th>
            <th class="text-center">Jenis</th>
            <th class="text-center">NIP Operator</th>
            <th class="text-center">Total Kuota</th>
            <th class="text-center">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($sekolahs as $sekolah)
            <tr>
                <th class="text-center">{{ $loop->iteration }}</th>
                <td>
                    <div class="font-medium text-gray-900 text-center">{{ $sekolah->nama_sekolah }}</div>
                    <div class="text-xs text-gray-500 text-center">NPSN: {{ $sekolah->npsn }}</div>
                </td>
                <td class="text-center">
                    <span class="badge {{ $sekolah->isSmk() ? 'badge-secondary' : 'badge-primary' }} badge-sm font-semibold">
                        {{ $sekolah->jenis_sekolah ?? 'SMA' }}
                    </span>
                    @if ($sekolah->isSmk() && $sekolah->jurusans->isNotEmpty())
                        <div class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                            {{ $sekolah->jurusans->pluck('nama_jurusan')->join(', ') }}
                        </div>
                    @endif
                </td>
                <td class="text-center">{{ $sekolah->operator?->nip ?? '-' }}</td>
                <td class="text-center">{{ $sekolah->kuota }}</td>
                <td class="text-center">
                    <ul class="menu menu-horizontal bg-base-200 rounded-box">
                        <li>
                            <a class="tooltip" data-tip="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="currentColor" d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83l3.75 3.75z" />
                                </svg>

                            </a>
                        </li>
                        <li>
                            <a class="tooltip" data-tip="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z" />
                                </svg>

                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-8 text-gray-500">
                    Belum ada data sekolah. Silakan klik tombol "Tambah" untuk menambahkan sekolah baru.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
