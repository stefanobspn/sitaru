<div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('index') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    Kembali ke Beranda
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1">Jurnal Hasil Seleksi SPMB</h1>
            </div>
            <div>
                <a href="{{ route('siswa') }}" class="btn btn-primary btn-sm text-white text-xs">
                    Masuk ke Akun Siswa
                </a>
            </div>
        </div>

        {{-- Filter Box --}}
        <div class="bg-white border border-slate-300 rounded-sm p-4 sm:p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="{{ $selectedSekolah?->isSmk() ? 'md:col-span-1' : 'md:col-span-2' }}">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Pilih Sekolah Tujuan
                    </label>
                    <select wire:model.live="selectedSekolahId" class="select select-bordered w-full text-sm">
                        @foreach ($sekolahs as $sekolah)
                            <option value="{{ $sekolah->id }}">
                                {{ $sekolah->nama_sekolah }} (Kuota: {{ $sekolah->kuota }} siswa)
                            </option>
                        @endforeach
                    </select>
                </div>
                @if ($selectedSekolah?->isSmk())
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Filter Jurusan
                        </label>
                        <select wire:model.live="selectedJurusanId" class="select select-bordered w-full text-sm">
                            <option value="">-- Semua Jurusan --</option>
                            @foreach ($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}">
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1" for="search_siswa">
                        Cari Siswa
                    </label>
                    <input
                        id="search_siswa"
                        name="search_siswa"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="input input-bordered w-full text-sm"
                        placeholder="Nama, NISN, atau No. Reg"
                    />
                </div>
            </div>
        </div>

        @if ($selectedSekolah)
            {{-- Statistik Kuota Sekolah --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="bg-white border border-slate-200 rounded-sm p-4">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Sekolah Terpilih</span>
                    <div class="font-bold text-gray-800 text-sm sm:text-base mt-1 truncate" title="{{ $selectedSekolah->nama_sekolah }}">
                        {{ $selectedSekolah->nama_sekolah }}
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-sm p-4">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Total Daya Tampung</span>
                    <div class="font-bold text-lg sm:text-xl mt-0.5">
                        {{ $selectedSekolah->kuota }} <span class="text-xs text-gray-500 font-normal">siswa</span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-sm p-4">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Total Pendaftar</span>
                    <div class="font-bold text-gray-800 text-lg sm:text-xl mt-0.5">
                        {{ $totalPendaftar }} <span class="text-xs text-gray-500 font-normal">siswa</span>
                    </div>
                </div>
            </div>

            {{-- Tabel Peringkat Seleksi --}}
            <div class="bg-white border border-slate-200 rounded-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full text-xs sm:text-sm">
                        <thead class="bg-slate-100 text-gray-700 font-bold uppercase text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="w-12 text-center">Rank</th>
                                <th class="text-center">No. Pendaftaran</th>
                                <th class="text-center">Nama Calon Siswa</th>
                                @if ($selectedSekolah->isSmk())
                                    <th class="text-center">Jurusan</th>
                                @endif
                                <th class="text-center" title="Nilai Akhir = 50% NRR + 50% NRTKA + NK + NO">Nilai Akhir (NA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftarList as $pendaftar)
                                @php
                                    $rank = $pendaftar->rank ?? $loop->iteration;
                                    $isPilihan1 = ($pendaftar->pilihan_sekolah_1_id === $selectedSekolah->id);
                                @endphp
                                <tr>
                                    <td class="text-center font-bold">
                                        {{ $rank }}
                                    </td>
                                    <td class="font-mono text-xs text-center">{{ $pendaftar->nomor_pendaftaran }}</td>
                                    <td class="font-semibold text-gray-800 text-center">
                                        {{ $pendaftar->nama_lengkap }}
                                    </td>
                                    @if ($selectedSekolah->isSmk())
                                        <td class="text-center text-xs font-medium text-gray-700">
                                            {{ $pendaftar->jurusan_terpilih?->nama_jurusan ?? ($isPilihan1 ? $pendaftar->pilihanJurusan1?->nama_jurusan : $pendaftar->pilihanJurusan2?->nama_jurusan) ?? '-' }}
                                        </td>
                                    @endif
                                    <td class="text-center font-bold text-sm">
                                        {{ number_format($pendaftar->nilai_akhir ?? $pendaftar->rata_rata_nilai_rapor, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selectedSekolah->isSmk() ? 5 : 4 }}" class="text-center py-8 text-gray-500 text-sm">
                                        @if ($selectedSekolah->isSmk() && $selectedJurusanId)
                                            Belum ada calon siswa yang masuk kuota pada jurusan ini.
                                        @else
                                            Belum ada calon siswa yang masuk kuota di sekolah ini.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-sm p-8 text-center text-gray-500 text-sm">
                Belum ada data sekolah yang terdaftar.
            </div>
        @endif
    </div>
</div>
