<div class="flex h-screen bg-slate-50">
    <x-sidebar-dashboard :active-menu="$activeMenu"/>
    <main class="flex-1 overflow-y-auto p-6">
        @if (session()->has('success') || $resetSuccessMessage)
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="alert"
                class="alert alert-success text-sm py-2 px-4 rounded-sm mb-4"
            >
                @if ($resetSuccessMessage)
                    <div class="flex flex-col">
                        <span class="font-bold text-emerald-900">Reset Password Berhasil!</span>
                        <span class="text-xs text-emerald-800 mt-0.5">{{ $resetSuccessMessage }}</span>
                    </div>
                @else
                    <span>{{ session('success') }}</span>
                @endif
            </div>
        @endif

        @if (session()->has('error') || $resetErrorMessage)
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="alert"
                class="alert alert-error text-white text-sm py-2 px-4 rounded-sm mb-4"
            >
                <span>{{ $resetErrorMessage ?: session('error') }}</span>
            </div>
        @endif

        @if (session()->has('info'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="alert"
                class="alert alert-warning text-sm py-2 px-4 rounded-sm mb-4"
            >
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if ($activeMenu === 'beranda')
            <div class="space-y-6">
                <div>
                    <span class="font-medium text-gray-500 text-sm">Selamat Datang</span>
                    <h1 class="font-bold text-2xl text-gray-800">Operator {{ Auth::user()?->sekolah?->nama_sekolah ?? 'Sekolah' }}</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola verifikasi akun, pantau seleksi pendaftar, dan kelola kuota penerimaan sekolah.</p>
                </div>

                {{-- Status Agenda Banner --}}
                <div class="bg-white border border-slate-300 rounded-sm p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-sm bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 tracking-wider">Tahapan PPDB Saat Ini</div>
                            <div class="text-sm font-bold text-gray-800">Verifikasi Berkas & Persiapan Seleksi</div>
                        </div>
                    </div>
                    <div class="text-xs text-right text-gray-500">
                        Jadwal Daftar Ulang: <strong class="text-gray-800 font-bold">26 Juni 2026</strong>
                    </div>
                </div>

                {{-- Grid Statistik Utama --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Peminat Pilihan 1</span>
                        <div class="font-bold text-gray-800 text-2xl mt-1">{{ number_format($pendaftarPilihan1Count) }}</div>
                        <span class="text-[10px] text-gray-400">Calon siswa prioritas utama</span>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Peminat Pilihan 2</span>
                        <div class="font-bold text-gray-800 text-2xl mt-1">{{ number_format($pendaftarPilihan2Count) }}</div>
                        <span class="text-[10px] text-gray-400">Calon siswa pilihan cadangan</span>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Daya Tampung (Kuota)</span>
                        <div class="font-bold text-blue-600 text-2xl mt-1">{{ number_format($kuotaSekolah) }}</div>
                        <span class="text-[10px] text-gray-400">{{ number_format($siswaDaftarUlangList->count()) }} siswa lolos sementara</span>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Verifikasi oleh Anda</span>
                        <div class="font-bold text-emerald-600 text-2xl mt-1">{{ number_format($diverifikasiOlehOperatorCount) }}</div>
                        <span class="text-[10px] text-gray-400">Siswa dengan NIP {{ Auth::user()->nip }}</span>
                    </div>
                </div>

                {{-- Status Daftar Ulang & Aksi Cepat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white border border-slate-200 rounded-sm p-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Status Daftar Ulang Siswa Lolos</span>
                            <div class="text-sm text-gray-700 mt-1">
                                <span class="font-bold text-emerald-600">{{ $sudahDaftarUlangCount }}</span> Sudah Hadir &bull;
                                <span class="font-bold text-gray-500">{{ $belumDaftarUlangCount }}</span> Belum Konfirmasi
                            </div>
                        </div>
                        <div>
                            @if ($isDaftarUlangOpen)
                                <span class="badge badge-success text-white text-xs">Jadwal Aktif</span>
                            @else
                                <span class="badge badge-ghost text-gray-500 text-xs">Mulai 26 Juni</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Aksi Cepat</span>
                            <div class="flex items-center gap-2 mt-2">
                                <button type="button" wire:click="openVerificationForm" class="btn btn-primary btn-xs text-white">
                                    Verifikasi Berkas
                                </button>
                                <button type="button" wire:click="openDaftarUlang" class="btn btn-outline btn-xs border-slate-300">
                                    Hasil Seleksi
                                </button>
                                <button type="button" wire:click="openRiwayatVerifikasi" class="btn btn-ghost btn-xs text-gray-600">
                                    Riwayat Verifikasi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jika SMK: Tampilkan Statistik Jurusan --}}
                @if ($jurusanStats->isNotEmpty())
                    <div class="bg-white border border-slate-200 rounded-sm p-5">
                        <h3 class="font-bold text-sm text-gray-800 mb-3">Distribusi Peminat Jurusan (SMK)</h3>
                        <div class="overflow-x-auto">
                            <table class="table table-sm w-full text-xs">
                                <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th>Nama Jurusan / Program Keahlian</th>
                                        <th class="text-center">Pilihan 1</th>
                                        <th class="text-center">Pilihan 2</th>
                                        <th class="text-center">Total Peminat</th>
                                        <th class="text-center">Lolos Sementara</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jurusanStats as $jStat)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                                            <td class="font-semibold text-gray-800">{{ $jStat['nama_jurusan'] }}</td>
                                            <td class="text-center">{{ number_format($jStat['peminat_1']) }}</td>
                                            <td class="text-center">{{ number_format($jStat['peminat_2']) }}</td>
                                            <td class="text-center font-bold">{{ number_format($jStat['peminat_1'] + $jStat['peminat_2']) }}</td>
                                            <td class="text-center font-semibold text-emerald-600">{{ number_format($jStat['diterima']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @elseif ($activeMenu === 'manage')
            @if ($isVerifying)
                <div>
                    <div class="flex flex-col mb-6">
                        <h1 class="font-bold text-2xl text-gray-800">Verifikasi Pendaftaran Siswa</h1>
                        <a
                            class="underline text-blue-500 cursor-pointer w-fit mt-1 text-sm"
                            wire:click="closeVerificationForm"
                        >
                            Kembali ke Menu Manajemen
                        </a>
                    </div>

                    @if (! $isVerifikasiOpen)
                        <div class="alert {{ $verifikasiStatus === 'closed' ? 'alert-warning' : 'alert-info text-white' }} text-xs py-3 px-4 rounded-sm mb-6 max-w-xl">
                            <span>{{ $verifikasiErrorMessage }}</span>
                        </div>
                    @endif

                    <div class="bg-white p-6 rounded-sm border border-slate-300 max-w-xl mb-6">
                        <form wire:submit="cariSiswa" class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                            <div class="flex-1 w-full">
                                <x-input
                                    name="searchNisn"
                                    wire:model="searchNisn"
                                    legend="NISN Siswa"
                                    placeholder="Contoh: 0012345678"
                                    required
                                />
                            </div>
                            <button type="submit" class="btn btn-primary sm:mb-2">
                                <span wire:loading.remove wire:target="cariSiswa">Cari Data</span>
                                <span wire:loading wire:target="cariSiswa">Mencari...</span>
                            </button>
                        </form>
                    </div>

                    @if ($foundSiswa)
                        <div class="bg-white rounded-sm border border-slate-300 max-w-3xl p-6 mb-8">
                            <div class="flex flex-wrap items-center justify-between pb-4 border-b border-slate-200 gap-3">
                                <div>
                                    <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Nomor Pendaftaran</span>
                                    <div class="text-lg font-bold text-gray-800">{{ $foundSiswa->nomor_pendaftaran }}</div>
                                </div>
                                <div>
                                    @if ($foundSiswa->status_verifikasi === 'terverifikasi')
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="badge badge-success text-white py-3 px-4 font-semibold">Sudah Terverifikasi / Aktif</span>
                                            @if ($foundSiswa->activation_code)
                                                <span class="text-xs font-mono font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-300">
                                                    Kode: {{ $foundSiswa->activation_code }}
                                                </span>
                                            @endif
                                            @if ($foundSiswa->operator_nip === Auth::user()?->nip && $foundSiswa->user)
                                                <button
                                                    type="button"
                                                    wire:click="openResetPasswordModal({{ $foundSiswa->id }})"
                                                    class="btn btn-warning btn-xs text-white font-semibold mt-1"
                                                >
                                                    Reset Password Akun
                                                </button>
                                            @elseif ($foundSiswa->operator_nip && $foundSiswa->operator_nip !== Auth::user()?->nip)
                                                <span class="text-[11px] text-gray-500 mt-1">
                                                    Diverifikasi Operator NIP: {{ $foundSiswa->operator_nip }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif ($foundSiswa->status_verifikasi === 'ditolak')
                                        <span class="badge badge-error text-white py-3 px-4 font-semibold">Pendaftaran Ditolak</span>
                                    @else
                                        <span class="badge badge-warning text-white py-3 px-4 font-semibold">Menunggu Verifikasi</span>
                                    @endif
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6 text-sm">
                                <div class="space-y-3">
                                    <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs">Identitas & Sekolah Asal</h3>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Nama Lengkap</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nama_lengkap }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">NISN</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nisn }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">NIK</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nik }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Sekolah Asal</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nama_sekolah_asal }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Tahun Lulus</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->tahun_lulus }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">No. WhatsApp</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->no_whatsapp }}</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs">Domisili & Prestasi</h3>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Status Domisili</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->status_domisili }}</span>
                                    </div>
                                    @if($foundSiswa->status_domisili === \App\Enums\StatusDomisili::SesuaiKK->value)
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Tgl Cetak KK</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->tgl_cetak_kk?->format('d/m/Y') ?? '-' }}</span>
                                        </div>
                                    @elseif($foundSiswa->status_domisili === \App\Enums\StatusDomisili::SesuaiMutasiOrtuWali->value)
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Instansi Penugasan</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nama_instansi_mutasi ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">No. Surat Penugasan</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nomor_surat_penugasan ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Tgl. Surat Penugasan</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->tgl_surat_penugasan?->format('d/m/Y') ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">SK Domisili Desa</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nomor_sk_domisili ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Anak Guru / Tendik</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->is_anak_guru ? 'Ya' : 'Tidak' }}</span>
                                        </div>
                                    @elseif($foundSiswa->status_domisili === \App\Enums\StatusDomisili::SesuaiDomisiliPondok->value)
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Pondok Pesantren</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nama_pondok_pesantren ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">No. Izin EMIS/NSPP</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nomor_izin_pondok_emis ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Jenjang Pondok</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->jenjang_pondok ?? '-' }}</span>
                                        </div>
                                    @elseif($foundSiswa->status_domisili === \App\Enums\StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Panti Asuhan</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nama_panti_asuhan ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">No. Registrasi Dinsos</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->nomor_registrasi_dinsos ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-gray-500">Prioritas Panti</span>
                                            <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->prioritas_panti ?? '-' }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Rata-rata Rapor (NRR)</span>
                                        <span class="font-bold text-right">{{ number_format($foundSiswa->rata_rata_nilai_rapor, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Nilai TKA (NRTKA)</span>
                                        <span class="font-bold text-right">{{ $foundSiswa->nrtka !== null ? number_format($foundSiswa->nrtka, 2) : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500 font-semibold text-primary">Nilai Akhir (NA)</span>
                                        <span class="font-bold text-right text-primary">{{ $foundSiswa->nilai_akhir !== null ? number_format($foundSiswa->nilai_akhir, 2) : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Status Khusus</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->status_khusus }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Prestasi</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->status_prestasi }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Organisasi</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $foundSiswa->status_kepengurusan_organisasi }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-200">
                                <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs mb-3">Dokumen yang Diunggah</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                        <span class="text-gray-700">Surat Pernyataan Kebenaran</span>
                                        @if ($foundSiswa->surat_pernyataan_kebenaran_path)
                                            <a href="{{ asset('storage/' . $foundSiswa->surat_pernyataan_kebenaran_path) }}" target="_blank" class="text-blue-600 underline font-medium text-xs">Lihat PDF</a>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                        <span class="text-gray-700">Kartu Keluarga (KK)</span>
                                        @if ($foundSiswa->kartu_keluarga_path)
                                            <a href="{{ asset('storage/' . $foundSiswa->kartu_keluarga_path) }}" target="_blank" class="text-blue-600 underline font-medium text-xs">Lihat PDF</a>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                        <span class="text-gray-700">Surat Keterangan Nilai Rapor</span>
                                        @if ($foundSiswa->surat_keterangan_nilai_rapor_path)
                                            <a href="{{ asset('storage/' . $foundSiswa->surat_keterangan_nilai_rapor_path) }}" target="_blank" class="text-blue-600 underline font-medium text-xs">Lihat PDF</a>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                    @if ($foundSiswa->piagam_prestasi_path)
                                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                            <span class="text-gray-700">Piagam Prestasi</span>
                                            <a href="{{ asset('storage/' . $foundSiswa->piagam_prestasi_path) }}" target="_blank" class="text-blue-600 underline font-medium text-xs">Lihat PDF</a>
                                        </div>
                                    @endif
                                    @if ($foundSiswa->sk_organisasi_path)
                                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-sm">
                                            <span class="text-gray-700">SK Organisasi</span>
                                            <a href="{{ asset('storage/' . $foundSiswa->sk_organisasi_path) }}" target="_blank" class="text-blue-600 underline font-medium text-xs">Lihat PDF</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-slate-300">
                                <label class="flex items-center gap-3 cursor-pointer select-none mb-3" for="persetujuan_tanggung_jawab">
                                    <input
                                        type="checkbox"
                                        id="persetujuan_tanggung_jawab"
                                        name="persetujuan_tanggung_jawab"
                                        wire:model="persetujuanTanggungJawab"
                                        class="checkbox checkbox-primary"
                                    />
                                    <span class="text-sm font-semibold text-gray-800">
                                        Saya paham pilihan saya, saya bersedia bertanggung jawab
                                    </span>
                                </label>
                                @error('persetujuanTanggungJawab')
                                    <p class="text-error text-sm font-medium mb-4">{{ $message }}</p>
                                @enderror

                                <div class="flex flex-wrap gap-3 mt-4">
                                    <button
                                        type="button"
                                        wire:click="verifikasiSiswa"
                                        class="btn btn-success text-white"
                                        @disabled(! $isVerifikasiOpen)
                                    >
                                        <span wire:loading.remove wire:target="verifikasiSiswa">Verifikasi (Sesuai)</span>
                                        <span wire:loading wire:target="verifikasiSiswa">Memproses...</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="tolakSiswa"
                                        class="btn btn-error text-white"
                                        @disabled(! $isVerifikasiOpen)
                                    >
                                        <span wire:loading.remove wire:target="tolakSiswa">Tolak</span>
                                        <span wire:loading wire:target="tolakSiswa">Memproses...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif ($isDaftarUlang)
                <div>
                    <div class="flex flex-col mb-6">
                        <h1 class="font-bold text-2xl text-gray-800">Konfirmasi Daftar Ulang Siswa</h1>
                        <a
                            class="underline text-blue-500 cursor-pointer w-fit mt-2 text-sm"
                            wire:click="closeDaftarUlang"
                        >
                            Kembali ke Menu Manajemen
                        </a>
                    </div>

                    {{-- Banner Jadwal Daftar Ulang --}}
                    <div class="p-4 mb-4 rounded-sm border {{ $isDaftarUlangOpen ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }} text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold">Jadwal Agenda Daftar Ulang:</span>
                            <span>26 Juni 2026</span>
                        </div>
                        <div>
                            @if ($isDaftarUlangOpen)
                                <span class="badge badge-success text-white font-semibold text-[11px]">Jadwal Berlangsung</span>
                            @else
                                <span class="badge badge-warning text-white font-semibold text-[11px]">Belum Dibuka (Mulai 26 Juni 2026)</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full text-xs sm:text-sm">
                                <thead class="bg-slate-100 text-gray-700 font-bold uppercase text-[11px] border-b border-slate-200">
                                    <tr>
                                        <th class="w-12 text-center">Rank</th>
                                        <th>No. Pendaftaran</th>
                                        <th>Nama Calon Siswa</th>
                                        <th>Sekolah Asal</th>
                                        <th class="text-center">Nilai Akhir (NA)</th>
                                        <th class="text-center">Status Daftar Ulang</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($siswaDaftarUlangList as $index => $siswaLolos)
                                        <tr>
                                            <td class="text-center font-bold ">{{ $index + 1 }}</td>
                                            <td class="font-mono text-xs">{{ $siswaLolos->nomor_pendaftaran }}</td>
                                            <td class="font-semibold text-gray-800">
                                                {{ $siswaLolos->nama_lengkap }}
                                                <div class="text-[10px] text-gray-400 font-mono">NISN: {{ $siswaLolos->nisn }}</div>
                                            </td>
                                            <td class="text-gray-600">{{ $siswaLolos->nama_sekolah_asal }}</td>
                                            <td class="text-center font-bold">{{ number_format($siswaLolos->nilai_akhir ?? $siswaLolos->rata_rata_nilai_rapor, 2) }}</td>
                                            <td class="text-center">
                                                @if ($siswaLolos->is_daftar_ulang)
                                                    <span class="badge badge-success text-white text-[11px] py-2 px-3 font-semibold">
                                                        Sudah Daftar Ulang
                                                    </span>
                                                @else
                                                    <span class="badge badge-ghost text-gray-500 text-[11px] py-2 px-3 font-medium">
                                                        Belum Konfirmasi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (! $siswaLolos->is_daftar_ulang)
                                                    @if (! $isDaftarUlangOpen)
                                                        <span class="badge badge-outline text-gray-400 text-[10px] font-medium" title="Konfirmasi dibuka pada jadwal resmi (26 Juni 2026)">
                                                            Dibuka 26 Juni
                                                        </span>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="konfirmasiDaftarUlang({{ $siswaLolos->id }})"
                                                            class="btn btn-success btn-xs text-white"
                                                        >
                                                            Konfirmasi Hadir
                                                        </button>
                                                    @endif
                                                @else
                                                    @if (! $isDaftarUlangOpen)
                                                        <span class="badge badge-success text-white text-[10px] font-medium">Hadir</span>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="batalkanDaftarUlang({{ $siswaLolos->id }})"
                                                            class="btn btn-ghost btn-xs text-red-500 hover:bg-red-50"
                                                        >
                                                            Batal
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-8 text-gray-500 text-sm">
                                                Belum ada pendaftar yang masuk dalam kuota sekolah ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif ($isEditingKuota)
                <div>
                    <div class="flex flex-col mb-6">
                        <h1 class="font-bold text-2xl text-gray-800">Edit Kuota Penerimaan Siswa</h1>
                        <a
                            class="underline text-blue-500 cursor-pointer w-fit mt-1 text-sm"
                            wire:click="closeEditKuotaForm"
                        >
                            Kembali ke Menu Manajemen
                        </a>
                    </div>



                    <div class="bg-white p-6 rounded-sm border border-slate-300 max-w-lg">
                        <div class="mb-4 text-sm text-gray-600">
                            <div>Sekolah: <strong class="text-gray-800">{{ Auth::user()?->sekolah?->nama_sekolah }}</strong></div>
                            <div>NPSN: <span class="font-mono text-gray-700">{{ Auth::user()?->sekolah?->npsn }}</span></div>
                            <div>Kuota Saat Ini: <strong class="font-bold">{{ Auth::user()?->sekolah?->kuota }} siswa</strong></div>
                        </div>

                        <form wire:submit="simpanKuota" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1" for="input_kuota">
                                    Jumlah Daya Tampung / Kuota Baru <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="input_kuota"
                                    name="kuota"
                                    wire:model="kuota"
                                    class="input input-bordered w-full text-sm font-semibold"
                                    min="1"
                                    max="5000"
                                    required
                                />
                                @error('kuota')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-2 pt-2">
                                <button
                                    type="button"
                                    wire:click="closeEditKuotaForm"
                                    class="btn btn-ghost border border-slate-300 btn-sm text-xs"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm text-white text-xs font-semibold"
                                >
                                    <span wire:loading.remove wire:target="simpanKuota">Simpan Perubahan Kuota</span>
                                    <span wire:loading wire:target="simpanKuota">Menyimpan...</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            @elseif ($isRiwayatVerifikasi)
                <div>
                    <div class="flex flex-col mb-6">
                        <h1 class="font-bold text-2xl text-gray-800">Riwayat Verifikasi dan Akun Calon Siswa</h1>
                        <a

                            class="underline text-blue-500 cursor-pointer w-fit mt-1 text-sm"
                            wire:click="closeRiwayatVerifikasi"
                        >
                            Kembali ke Menu Manajemen
                        </a>
                    </div>


                    @if ($resetTargetSiswaId)
                        @php $targetSiswa = $riwayatSiswaList->firstWhere('id', $resetTargetSiswaId); @endphp
                        <div class="bg-amber-50 border border-amber-300 p-5 rounded-sm mb-6 max-w-xl">
                            <h3 class="font-bold text-amber-900 text-sm mb-1">Konfirmasi Reset Password Siswa</h3>
                            <p class="text-xs text-amber-800 mb-3">
                                Anda akan mereset password untuk <strong class="font-semibold">{{ $targetSiswa?->nama_lengkap }}</strong> (NISN: <span class="font-mono">{{ $targetSiswa?->nisn }}</span>).
                            </p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1" for="new_password">
                                        Password Baru (Minimal 6 Karakter)
                                    </label>
                                    <input
                                        type="text"
                                        id="new_password"
                                        name="new_password"
                                        wire:model="newPassword"
                                        class="input input-bordered input-sm w-full font-mono text-sm"
                                        placeholder="Default: password123"
                                    />
                                    @error('newPassword')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                    <span class="text-[11px] text-gray-500 block mt-1">Biarkan default <code>password123</code> atau ketik password baru.</span>
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <button
                                        type="button"
                                        wire:click="closeResetPasswordModal"
                                        class="btn btn-ghost btn-sm text-xs border border-slate-300"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="resetPasswordSiswa({{ $resetTargetSiswaId }})"
                                        class="btn btn-warning btn-sm text-white font-semibold text-xs"
                                    >
                                        Simpan & Reset Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white rounded-sm border border-slate-300 p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between pb-4 border-b border-slate-200 gap-3">
                            <div class="text-sm text-gray-600">
                                Total Siswa Diverifikasi: <strong class="text-gray-900 font-bold">{{ $riwayatSiswaList->count() }}</strong> calon siswa
                            </div>
                            <div class="w-full sm:w-72">
                                <label for="search_riwayat" class="sr-only">Cari Riwayat</label>
                                <input
                                    type="text"
                                    id="search_riwayat"
                                    name="search_riwayat"
                                    wire:model.live.debounce.300ms="searchRiwayat"
                                    class="input input-bordered input-sm w-full text-xs"
                                    placeholder="Cari Nama, NISN, atau No. Reg..."
                                />
                            </div>
                        </div>

                        <div class="overflow-x-auto mt-4">
                            <table class="table table-sm w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-100 text-gray-700">
                                        <th class="py-2.5">No</th>
                                        <th>No. Pendaftaran</th>
                                        <th>NISN & Nama Lengkap</th>
                                        <th>Sekolah Asal</th>
                                        <th>Status Akun</th>
                                        <th class="text-center">Aksi / Hak Reset</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($riwayatSiswaList as $index => $siswa)
                                        <tr class="hover:bg-slate-50 border-b border-slate-100">
                                            <td>{{ $index + 1 }}</td>
                                            <td class="font-mono font-semibold text-gray-800">{{ $siswa->nomor_pendaftaran }}</td>
                                            <td>
                                                <div class="font-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                                <div class="font-mono text-[11px] text-gray-500">NISN: {{ $siswa->nisn }}</div>
                                            </td>
                                            <td>{{ $siswa->nama_sekolah_asal }}</td>
                                            <td>
                                                @if ($siswa->user)
                                                    <span class="badge badge-success text-white text-[10px] font-semibold">Akun Aktif</span>
                                                @else
                                                    <span class="badge badge-warning text-white text-[10px] font-semibold">Belum Aktivasi</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($siswa->user)
                                                    <button
                                                        type="button"
                                                        wire:click="openResetPasswordModal({{ $siswa->id }})"
                                                        class="btn btn-warning btn-xs text-white font-semibold"
                                                    >
                                                        Reset Password
                                                    </button>
                                                @else
                                                    <span class="text-[11px] text-gray-400 font-mono" title="Kode Aktivasi">
                                                        Kode: {{ $siswa->activation_code }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-8 text-gray-500 text-sm">
                                                @if ($searchRiwayat)
                                                    Tidak ditemukan siswa diverifikasi yang cocok dengan kata kunci "{{ $searchRiwayat }}".
                                                @else
                                                    Belum ada calon siswa yang diverifikasi oleh NIP Anda ({{ Auth::user()?->nip }}).
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div>


                    <h1 class="font-bold text-2xl text-gray-800">Manajemen {{ Auth::user()?->sekolah?->nama_sekolah }}</h1>

                    <div class="mt-2 text-sm text-gray-500">
                        <div>
                        NPSN: <span class="font-mono font-medium text-gray-700">{{ Auth::user()?->sekolah?->npsn }}</span>
                        </div>
                        <div>
                            Daya Tampung / Kuota: {{ Auth::user()?->sekolah?->kuota }} siswa
                        </div>
                    </div>

                    <ul class="menu menu-vertical lg:menu-horizontal bg-base-200 gap-3 mt-6">
                        <li class="border rounded-sm border-slate-300">
                            <a wire:click="openVerificationForm" class="cursor-pointer font-medium">Verifikasi Berkas</a>
                        </li>
                        <li class="border rounded-sm border-slate-300">
                            <a wire:click="openDaftarUlang" class="cursor-pointer font-medium">Daftar Ulang Siswa</a>
                        </li>
                        <li class="border rounded-sm border-slate-300">
                            <a wire:click="openEditKuotaForm" class="cursor-pointer font-medium">Edit Kuota Sekolah</a>
                        </li>
                        <li class="border rounded-sm border-slate-300">
                            <a wire:click="openRiwayatVerifikasi" class="cursor-pointer font-medium">Riwayat Verifikasi dan Reset Password</a>
                        </li>

                    </ul>
                </div>
            @endif
        @endif
    </main>
</div>

