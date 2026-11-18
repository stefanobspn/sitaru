<div class="min-h-screen bg-slate-50">
    {{-- Top Navbar --}}
    <header class="bg-white border-b border-slate-200 px-4 sm:px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="font-bold text-lg text-blue-900 tracking-tight">SITARU</span>
            <span class="text-xs text-gray-400">|</span>
            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Portal Siswa</span>
        </div>

        <div class="flex items-center gap-4">
            <button
                type="button"
                wire:click="logout"
                class="btn btn-outline btn-sm text-xs border-slate-300 hover:bg-red-50 hover:text-red-600 hover:border-red-300"
            >
                Logout
            </button>
        </div>
    </header>

    <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6">
        @if (session()->has('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="alert"
                class="alert alert-success text-sm py-3 px-4 rounded-sm mb-6"
            >
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="alert"
                class="alert alert-error text-white text-sm py-3 px-4 rounded-sm mb-6"
            >
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Banner Jadwal Agenda --}}
        <div class="p-4 mb-6 rounded-sm border {{ $pendaftaranSekolahStatus === 'active' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($pendaftaranSekolahStatus === 'not_started' ? 'bg-amber-50 border-amber-200 text-amber-800' : ($isDaftarUlangOpen ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($isHasilSeleksiOpen ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-slate-100 border-slate-300 text-slate-800'))) }} text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                @if ($isDaftarUlangOpen)
                    <span class="font-bold">Agenda Saat Ini:</span>
                    <span>Daftar Ulang Siswa Lolos (Mulai 26 Juni 2026)</span>
                @elseif ($isHasilSeleksiOpen)
                    <span class="font-bold">Agenda Saat Ini:</span>
                    <span>Pengumuman Hasil Seleksi (19 - 21 Juni 2026)</span>
                @else
                    <span class="font-bold">Jadwal Agenda Pendaftaran Sekolah:</span>
                    <span>15 - 18 Juni 2026</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if ($pendaftaranSekolahStatus === 'active')
                    <span class="badge badge-success text-white font-semibold text-[11px]">Jadwal Berlangsung</span>
                @elseif ($pendaftaranSekolahStatus === 'not_started')
                    <span class="badge badge-warning text-white font-semibold text-[11px]">Belum Dibuka (Mulai 15 Juni 2026)</span>
                @else
                    <span class="badge badge-neutral text-white font-semibold text-[11px]">Telah Ditutup (Selesai 18 Juni 2026)</span>
                    @if ($isDaftarUlangOpen)
                        <span class="badge badge-success text-white font-semibold text-[11px]">Tahap Daftar Ulang</span>
                    @elseif ($isHasilSeleksiOpen)
                        <span class="badge badge-info text-white font-semibold text-[11px]">Pengumuman Seleksi</span>
                    @endif
                @endif
            </div>
        </div>

        {{-- Status Banner --}}
        <div class="bg-white border border-slate-200 rounded-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mt-0.5">
                        Selamat Datang, {{ Auth::user()->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                        Akun Anda telah berhasil diaktivasi dan dinyatakan terverifikasi oleh operator sekolah.
                    </p>
                </div>
            </div>
        </div>

        @if ($isChoosingSchool)
            {{-- Form Pemilihan Sekolah --}}
            <div class="bg-white border border-slate-300 rounded-sm p-6 mb-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Pendaftaran & Pemilihan Sekolah Tujuan</h2>
                    </div>
                </div>

                <form wire:submit="simpanPilihanSekolah" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Sekolah Pilihan 1 <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="pilihan_sekolah_1_id" class="select select-bordered w-full text-sm">
                            <option value="">-- Pilih Sekolah Pilihan 1 --</option>
                            @foreach ($sekolahs as $sekolah)
                                <option value="{{ $sekolah->id }}">
                                    [{{ $sekolah->jenis_sekolah ?? 'SMA' }}] {{ $sekolah->nama_sekolah }} (Kuota: {{ $sekolah->kuota }})
                                </option>
                            @endforeach
                        </select>
                        @error('pilihan_sekolah_1_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        @php $sekolah1Selected = $sekolahs->firstWhere('id', $pilihan_sekolah_1_id); @endphp
                        @if ($sekolah1Selected?->isSmk())
                            <div class="mt-2 p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                    Pilih Jurusan / Program Keahlian (Pilihan 1) <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="pilihan_jurusan_1_id" class="select select-bordered w-full text-sm">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($sekolah1Selected->jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('pilihan_jurusan_1_id')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Sekolah Pilihan 2 <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        @php
                            $sekolah2Options = $sekolah1Selected
                                ? $sekolahs->where('jenis_sekolah', $sekolah1Selected->jenis_sekolah)->where('id', '!=', $sekolah1Selected->id)
                                : collect();
                        @endphp

                        @if ($sekolah1Selected)
                            <select wire:model.live="pilihan_sekolah_2_id" class="select select-bordered w-full text-sm">
                                <option value="">-- Pilih Sekolah Alternatif / Pilihan 2 (Opsional - {{ $sekolah1Selected->jenis_sekolah }}) --</option>
                                @foreach ($sekolah2Options as $sekolah)
                                    <option value="{{ $sekolah->id }}">
                                        [{{ $sekolah->jenis_sekolah ?? 'SMA' }}] {{ $sekolah->nama_sekolah }} (Kuota: {{ $sekolah->kuota }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1">
                                Pilihan 2 wajib memiliki jenis sekolah yang sama dengan Pilihan 1 ({{ $sekolah1Selected->jenis_sekolah }}).
                            </p>
                        @else
                            <select class="select select-bordered w-full text-sm" disabled>
                                <option value="">-- Pilih Sekolah Pilihan 1 Terlebih Dahulu --</option>
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1">
                                Silakan pilih Sekolah Pilihan 1 terlebih dahulu untuk menentukan jenis sekolah (SMA/SMK).
                            </p>
                        @endif
                        @error('pilihan_sekolah_2_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        @php $sekolah2Selected = $sekolahs->firstWhere('id', $pilihan_sekolah_2_id); @endphp
                        @if ($sekolah2Selected?->isSmk())
                            <div class="mt-2 p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                    Pilih Jurusan / Program Keahlian (Pilihan 2) <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="pilihan_jurusan_2_id" class="select select-bordered w-full text-sm">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($sekolah2Selected->jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('pilihan_jurusan_2_id')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row gap-2">
                        <button type="button" wire:click="tutupFormPilihanSekolah" class="btn btn-ghost border border-slate-300 order-2 sm:order-1">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary flex-1 text-white font-semibold order-1 sm:order-2">
                            <span wire:loading.remove wire:target="simpanPilihanSekolah">Simpan & Kunci Pilihan Sekolah</span>
                            <span wire:loading wire:target="simpanPilihanSekolah">Menyimpan Pilihan...</span>
                        </button>
                    </div>
                </form>
            </div>
        @elseif ($siswa?->pilihan_sekolah_1_id)
            {{-- Peringatan Real-Time Jika Tergeser di Kedua Pilihan --}}
            @if ($statusSeleksi && $statusSeleksi['is_tergeser_semua'])
                <div class="bg-amber-50 border border-amber-300 rounded-sm p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="badge badge-warning text-white text-[11px] font-bold uppercase tracking-wider mb-1">
                                Peringatan Seleksi
                            </span>
                            <h2 class="text-lg sm:text-xl font-bold text-amber-900 mt-1">
                                Perhatian: Nilai Anda Tergeser dari Kuota
                            </h2>
                            <p class="text-xs sm:text-sm text-amber-800 mt-1 leading-relaxed">
                                @if ($siswa->pilihan_sekolah_2_id)
                                    Nilai rata-rata rapor Anda (<strong class="font-bold">{{ $siswa->rata_rata_nilai_rapor }}</strong>) saat ini tergeser dari kuota di Pilihan 1 ({{ $siswa->pilihanSekolah1?->nama_sekolah }}) maupun Pilihan 2 ({{ $siswa->pilihanSekolah2?->nama_sekolah }}).
                                @else
                                    Nilai rata-rata rapor Anda (<strong class="font-bold">{{ $siswa->rata_rata_nilai_rapor }}</strong>) saat ini tergeser dari kuota di Pilihan 1 ({{ $siswa->pilihanSekolah1?->nama_sekolah }}).
                                @endif
                                Anda disarankan untuk mengubah pilihan sekolah ke sekolah lain yang kuotanya masih memenuhi selama masa pendaftaran dibuka.
                            </p>
                        </div>
                        @if ($siswa->status_seleksi === 'menunggu')
                            @if ($pendaftaranSekolahStatus === 'active')
                                <button
                                    type="button"
                                    wire:click="bukaFormPilihanSekolah"
                                    class="btn btn-warning text-white font-semibold text-xs whitespace-nowrap shrink-0"
                                >
                                    Ubah Pilihan Sekolah
                                </button>
                            @elseif ($pendaftaranSekolahStatus === 'not_started')
                                <span class="badge badge-outline text-amber-800 text-xs py-2 px-3 font-medium whitespace-nowrap shrink-0">
                                    Dibuka 15 Juni
                                </span>
                            @else
                                <span class="badge badge-outline text-amber-800 text-xs py-2 px-3 font-medium whitespace-nowrap shrink-0">
                                    Pendaftaran Ditutup
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            @elseif ($statusSeleksi && $statusSeleksi['status'] === 'lolos_pilihan_2')
                <div class="bg-blue-50 border border-blue-200 rounded-sm p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="badge badge-info text-white text-[10px] font-bold uppercase mt-0.5">Info Alokasi</span>
                        <div class="text-xs text-blue-900 leading-relaxed">
                            Nilai rapor Anda tergeser dari kuota di Pilihan 1 ({{ $siswa->pilihanSekolah1?->nama_sekolah }}). Anda otomatis dialihkan dan saat ini <strong>Masuk Kuota di Pilihan 2 ({{ $siswa->pilihanSekolah2?->nama_sekolah }})</strong> pada Peringkat {{ $statusSeleksi['rank'] }}.
                        </div>
                    </div>
                </div>
            @endif

            {{-- Banner Pengumuman Penerimaan Khusus Jika Sudah Final --}}
            @if ($siswa->status_seleksi === 'diterima_pilihan_1' || $siswa->status_seleksi === 'diterima_pilihan_2')
                <div class="bg-emerald-50 border border-emerald-300 rounded-sm p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-3">
                            <div>
                                <span class="badge badge-success text-white text-[11px] font-bold uppercase tracking-wider mb-1">
                                    Pengumuman Kelulusan SPMB
                                </span>
                                <h2 class="text-xl sm:text-2xl font-bold text-emerald-900 mt-1">
                                    SELAMAT! Anda Dinyatakan DITERIMA
                                </h2>
                                <p class="text-xs sm:text-sm text-emerald-800 mt-1 leading-relaxed">
                                    Anda berhasil lolos seleksi penerimaan murid baru di <strong class="font-bold underline">{{ $siswa->diterimaSekolah?->nama_sekolah }}</strong> melalui alokasi <span class="font-semibold">{{ $siswa->status_seleksi === 'diterima_pilihan_1' ? 'Pilihan 1' : 'Pilihan 2' }}</span>.
                                </p>
                            </div>

                            <div class="pt-3 border-t border-emerald-200 text-xs text-emerald-900 space-y-2">
                                <div class="font-bold flex items-center gap-1.5 text-emerald-950">
                                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Petunjuk Tahap Selanjutnya (Daftar Ulang):</span>
                                </div>

                                @if (! $isDaftarUlangOpen)
                                    <div class="bg-white/80 p-3.5 rounded-sm border border-emerald-200 leading-relaxed text-emerald-900">
                                        <p class="font-semibold text-emerald-950">
                                            Jadwal Daftar Ulang: Resmi dibuka mulai tanggal <span class="underline font-bold">26 Juni 2026</span> di sekolah tujuan.
                                        </p>
                                        <p class="mt-1 text-[11px] text-emerald-800">
                                            Saat ini (19 - 25 Juni 2026), Anda <strong>belum perlu hadir ke sekolah</strong>. Silakan persiapkan kelengkapan berkas fisik asli Anda untuk dibawa saat daftar ulang:
                                        </p>
                                        <ul class="list-disc list-inside mt-2 space-y-0.5 text-[11px] text-emerald-800 font-medium">
                                            <li>Surat Keterangan Lulus (SKL) / Ijazah SMP asli & fotokopi legalisir</li>
                                            <li>Buku Rapor SMP asli & fotokopi semester 1 s.d. 5</li>
                                            <li>Kartu Keluarga (KK) asli & fotokopi</li>
                                            <li>Akta Kelahiran asli & fotokopi</li>
                                            <li>Cetak Bukti Pendaftaran / Tanda Diterima SPMB</li>
                                            <li>Piagam Kejuaraan / Sertifikat Prestasi asli (jika ada)</li>
                                        </ul>
                                    </div>
                                @else
                                    <div class="bg-white/80 p-3.5 rounded-sm border border-emerald-300 leading-relaxed text-emerald-900">
                                        <p class="font-bold text-emerald-950">
                                            Tahap Daftar Ulang SEDANG BERLANGSUNG (Mulai 26 Juni 2026)!
                                        </p>
                                        <p class="mt-1 text-[11px] text-emerald-800">
                                            Silakan segera datang langsung ke <strong>{{ $siswa->diterimaSekolah?->nama_sekolah }}</strong> dengan membawa berkas asli & fotokopi persyaratan di atas untuk diverifikasi oleh operator sekolah.
                                        </p>
                                    </div>
                                @endif

                                <div class="pt-2 flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="cetakBuktiPendaftaran"
                                        class="btn btn-success text-white btn-sm text-xs font-semibold flex items-center gap-1.5"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Cetak Bukti Pendaftaran (PDF)</span>
                                    </button>
                                    @if ($siswa->nomor_pendaftaran)
                                        <a
                                            href="{{ route('pendaftaran.cetak-bukti', $siswa->nomor_pendaftaran) }}"
                                            target="_blank"
                                            class="btn btn-outline btn-sm text-xs text-emerald-800 border-emerald-300 hover:bg-emerald-100 flex items-center gap-1"
                                            title="Buka PDF di tab baru"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Buka PDF</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-col items-start sm:items-end gap-2">
                            @if ($siswa->is_daftar_ulang)
                                <div class="bg-white border border-emerald-400 rounded-sm p-3 text-center sm:text-right">
                                    <span class="text-[10px] font-bold uppercase text-emerald-700 block">Status Daftar Ulang</span>
                                    <span class="badge badge-success text-white text-xs font-bold mt-1">Sudah Daftar Ulang</span>
                                    <span class="text-[10px] text-gray-500 block mt-1">Dikonfirmasi oleh Operator</span>
                                </div>
                            @else
                                <div class="bg-white border border-amber-300 rounded-sm p-3 text-center sm:text-right">
                                    <span class="text-[10px] font-bold uppercase text-amber-700 block">Status Daftar Ulang</span>
                                    <span class="badge badge-warning text-white text-xs font-bold mt-1">Belum Hadir / Belum Konfirmasi</span>
                                    @if (! $isDaftarUlangOpen)
                                        <span class="text-[10px] text-blue-600 font-medium block mt-1">Dibuka 26 Juni 2026</span>
                                    @else
                                        <span class="text-[10px] text-gray-500 block mt-1">Harap segera daftar ulang</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif ($siswa->status_seleksi === 'tidak_lolos')
                <div class="bg-rose-50 border border-rose-300 rounded-sm p-6 mb-6">
                    <span class="badge badge-error text-white text-[11px] font-bold uppercase tracking-wider mb-1">
                        Pengumuman Hasil Seleksi
                    </span>
                    <h2 class="text-lg sm:text-xl font-bold text-rose-900 mt-1">
                        Mohon Maaf, Anda Belum Lolos Seleksi
                    </h2>
                    <p class="text-xs sm:text-sm text-rose-800 mt-1 leading-relaxed">
                        Nilai rata-rata rapor Anda (<strong class="font-bold">{{ $siswa->rata_rata_nilai_rapor }}</strong>) belum memenuhi kuota penerimaan pada pilihan sekolah yang Anda tuju tahun ini. Tetap semangat dalam melanjutkan pendidikan Anda.
                    </p>
                    <div class="mt-4 pt-3 border-t border-rose-200 text-xs text-rose-800 space-y-1">
                        <div class="font-semibold text-rose-950">Petunjuk & Informasi:</div>
                        <p class="text-[11px] leading-relaxed">
                            Hasil seleksi telah final berdasarkan kuota daya tampung sekolah tujuan. Anda dapat memantau jika ada pengumuman pemenuhan kuota cadangan pasca-daftar ulang (setelah 1 Juli 2026), atau mempertimbangkan pendaftaran sekolah swasta terdekat.
                        </p>
                    </div>
                </div>
            @elseif ($siswa->status_seleksi === 'menunggu' && $pendaftaranSekolahStatus === 'closed')
                {{-- Panduan Tahap Pengumuman Hasil Seleksi ketika Belum Difinalisasi --}}
                <div class="bg-blue-50 border border-blue-200 rounded-sm p-5 mb-6 text-xs text-blue-900">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-blue-100 rounded-full text-blue-700 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-bold text-sm text-blue-950">Tahap Pengumuman Hasil Seleksi (19 - 21 Juni 2026)</h3>
                            <p class="leading-relaxed text-blue-800">
                                Masa pendaftaran dan pemilihan sekolah tujuan telah resmi berakhir pada 18 Juni 2026 pukul 23:59 WIB. Saat ini panitia SPMB sedang melakukan proses rekapitulasi data dan penetapan kelulusan resmi.
                            </p>
                            <div class="pt-2 border-t border-blue-200">
                                <span class="font-bold text-blue-950 block mb-1">Petunjuk untuk Anda saat ini:</span>
                                <ul class="list-disc list-inside space-y-1 text-blue-800 text-[11px]">
                                    <li><strong>Pantau Peringkat:</strong> Anda dapat mengecek posisi sementara Anda pada pilihan sekolah melalui tombol <strong>Lihat Jurnal Ranking</strong> di bawah.</li>
                                    <li><strong>Pengumuman Final:</strong> Status kelulusan resmi akan segera diumumkan oleh Administrator / Panitia SPMB.</li>
                                    <li><strong>Daftar Ulang:</strong> Jika dinyatakan lolos pada pengumuman final, tahap Daftar Ulang resmi dibuka mulai <strong>26 Juni 2026</strong>.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status Pilihan Sekolah yang Sudah Dipilih --}}
            <div class="bg-white border border-slate-200 rounded-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Data Pilihan Sekolah</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($siswa->status_seleksi === 'diterima_pilihan_1' || $siswa->status_seleksi === 'diterima_pilihan_2')
                            <span class="badge badge-success text-white py-3 px-3 text-xs font-semibold">
                                DITERIMA di {{ $siswa->diterimaSekolah?->nama_sekolah }}
                            </span>
                        @elseif ($siswa->status_seleksi === 'tidak_lolos')
                            <span class="badge badge-error text-white py-3 px-3 text-xs font-semibold">
                                Tidak Lolos Seleksi
                            </span>
                        @elseif ($statusSeleksi && $statusSeleksi['is_tergeser_semua'])
                            <span class="badge badge-warning text-white py-3 px-3 text-xs font-semibold">
                                Tergeser dari Kuota
                            </span>
                        @elseif ($statusSeleksi && $statusSeleksi['status'] === 'lolos_pilihan_2')
                            <span class="badge badge-info text-white py-3 px-3 text-xs font-semibold">
                                Masuk Kuota Pilihan 2 (Rank {{ $statusSeleksi['rank'] }})
                            </span>
                        @elseif ($statusSeleksi && $statusSeleksi['status'] === 'lolos_pilihan_1')
                            <span class="badge badge-success text-white py-3 px-3 text-xs font-semibold">
                                Masuk Kuota Pilihan 1 (Rank {{ $statusSeleksi['rank'] }})
                            </span>
                        @else
                            <span class="badge badge-info text-white py-3 px-3 text-xs font-semibold">
                                Menunggu Hasil Seleksi
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-4">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Pilihan 1</span>
                        </div>
                        <div class="font-bold text-gray-800 text-base mt-1">
                            {{ $siswa->pilihanSekolah1?->nama_sekolah ?? '-' }}
                        </div>
                        @if ($siswa->pilihanSekolah1?->isSmk())
                            <div class="text-xs font-semibold mt-0.5">
                                Jurusan: {{ $siswa->pilihanJurusan1?->nama_jurusan ?? '-' }}
                            </div>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">Kuota: {{ $siswa->pilihanSekolah1?->kuota ?? '-' }} siswa</div>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Pilihan 2</span>
                        </div>
                        <div class="font-bold text-gray-800 text-base mt-1">
                            {{ $siswa->pilihanSekolah2?->nama_sekolah ?? 'Tidak Memilih Pilihan 2' }}
                        </div>
                        @if ($siswa->pilihanSekolah2?->isSmk())
                            <div class="text-xs font-semibold mt-0.5">
                                Jurusan: {{ $siswa->pilihanJurusan2?->nama_jurusan ?? '-' }}
                            </div>
                        @endif
                        @if ($siswa->pilihanSekolah2)
                            <div class="text-xs text-gray-500 mt-1">Kuota: {{ $siswa->pilihanSekolah2->kuota }} siswa</div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between pt-2 border-t border-slate-100 gap-3">
                    <div class="flex gap-2">
                        <a href="{{ route('seleksi') }}" class="btn btn-outline btn-sm text-xs border-slate-300">
                            Lihat Jurnal Ranking
                        </a>
                        @if ($siswa->status_seleksi === 'menunggu')
                            @if ($pendaftaranSekolahStatus === 'active')
                                <button type="button" wire:click="bukaFormPilihanSekolah" class="btn btn-ghost btn-sm text-xs text-blue-600 hover:underline">
                                    Ubah Pilihan
                                </button>
                            @elseif ($pendaftaranSekolahStatus === 'not_started')
                                <span class="badge badge-outline text-gray-400 text-xs font-medium">
                                    Dibuka 15 Juni
                                </span>
                            @else
                                <span class="badge badge-outline text-gray-400 text-xs font-medium">
                                    Pendaftaran Ditutup
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @else
            {{-- Ajakan Memilih Sekolah --}}
            <div class="{{ $pendaftaranSekolahStatus === 'active' ? 'bg-blue-50 border-blue-200' : 'bg-slate-100 border-slate-200' }} border rounded-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold {{ $pendaftaranSekolahStatus === 'active' ? 'text-blue-900' : 'text-gray-700' }}">
                            @if ($pendaftaranSekolahStatus === 'active')
                                Pendaftaran Sekolah Dibuka!
                            @elseif ($pendaftaranSekolahStatus === 'not_started')
                                Tahap Pendaftaran Sekolah Belum Dibuka
                            @else
                                Tahap Pendaftaran Sekolah Telah Ditutup
                            @endif
                        </h2>
                        <p class="text-xs sm:text-sm {{ $pendaftaranSekolahStatus === 'active' ? 'text-blue-800' : 'text-gray-500' }} mt-1 leading-relaxed">
                            @if ($pendaftaranSekolahStatus === 'active')
                                Silakan tentukan sekolah tujuan Anda.
                            @elseif ($pendaftaranSekolahStatus === 'not_started')
                                Pendaftaran dan pemilihan sekolah tujuan baru dapat dilakukan mulai tanggal 15 Juni 2026.
                            @else
                                Masa pendaftaran dan pemilihan sekolah tujuan telah berakhir pada tanggal 18 Juni 2026.
                            @endif
                        </p>
                    </div>
                    @if ($pendaftaranSekolahStatus === 'active')
                        <button
                            type="button"
                            wire:click="bukaFormPilihanSekolah"
                            class="btn btn-primary text-white font-semibold text-xs sm:text-sm whitespace-nowrap shrink-0"
                        >
                            Pilih Sekolah Tujuan Sekarang
                        </button>
                    @elseif ($pendaftaranSekolahStatus === 'not_started')
                        <span class="badge badge-outline text-gray-500 text-xs py-3 px-4 font-semibold whitespace-nowrap shrink-0">
                            Dibuka 15 Juni 2026
                        </span>
                    @else
                        <span class="badge badge-neutral text-white text-xs py-3 px-4 font-semibold whitespace-nowrap shrink-0">
                            Pendaftaran Ditutup
                        </span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Detail Data Calon Siswa --}}
        @if ($siswa)
            <div class="bg-white border border-slate-200 rounded-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Nomor Pendaftaran</span>
                        <div class="text-base sm:text-lg font-bold text-gray-800">{{ $siswa->nomor_pendaftaran }}</div>
                    </div>
                    @if ($siswa->nomor_pendaftaran)
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="cetakBuktiPendaftaran"
                                class="btn btn-primary btn-sm text-xs font-semibold text-white flex items-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Bukti Pendaftaran</span>
                            </button>
                            <a
                                href="{{ route('pendaftaran.cetak-bukti', $siswa->nomor_pendaftaran) }}"
                                target="_blank"
                                class="btn btn-outline btn-sm text-xs border-slate-300 flex items-center gap-1"
                                title="Buka PDF di tab baru"
                            >
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Lihat PDF</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6 text-sm">
                    <div class="space-y-3">
                        <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs">Identitas Calon Siswa</h3>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Nama Lengkap</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->nama_lengkap }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">NISN</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->nisn }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Sekolah Asal</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->nama_sekolah_asal }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Tahun Lulus</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->tahun_lulus }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">No. WhatsApp</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->no_whatsapp }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs">Akademik & Domisili</h3>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Status Domisili</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->status_domisili }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Rata-rata Nilai Rapor</span>
                            <span class="text-right font-semibold">{{ $siswa->rata_rata_nilai_rapor }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Status Khusus</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->status_khusus }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Prestasi</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $siswa->status_prestasi }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-gray-500">Verifikator (NIP Operator)</span>
                            <span class="font-mono text-gray-800 text-right text-xs">{{ $siswa->operator_nip ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
