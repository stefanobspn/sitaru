<div class="flex min-h-screen flex-col bg-slate-50 md:h-screen md:flex-row">
    <x-sidebar-dashboard :active-menu="$activeMenu"/>

    <main class="min-w-0 flex-1 overflow-y-auto p-4 sm:p-6">
        @if (session()->has('success'))
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
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div role="alert" class="alert alert-error text-sm py-2 px-4 rounded-sm mb-4">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($activeMenu === 'beranda')
            <div class="space-y-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Beranda SPMB Terpusat</h1>
                    <p class="text-xs text-gray-500 mt-1">Pusat kendali operasional pendaftaran dan penentuan hasil seleksi sekolah.</p>
                </div>

                {{-- Panel Pengumuman & Finalisasi Seleksi --}}
                <div class="bg-white border {{ $isFinalized ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-300' }} rounded-sm p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status Seleksi Nasional</span>
                                @if ($isFinalized)
                                    <span class="badge badge-success text-white text-xs font-semibold">Hasil Telah Diumumkan & Terkunci</span>
                                @else
                                    <span class="badge badge-warning text-white text-xs font-semibold">Proses Pendaftaran Masih Berjalan</span>
                                @endif
                            </div>
                            <h2 class="text-lg font-bold text-gray-800 mt-1">
                                {{ $isFinalized ? 'Hasil Seleksi Sudah Difinalisasi' : 'Kunci & Umumkan Hasil Seleksi Penerimaan' }}
                            </h2>
                            <p class="text-xs text-gray-600 mt-0.5 max-w-2xl leading-relaxed">
                                {{ $isFinalized 
                                    ? 'Status penerimaan siswa telah diproses ke masing-masing pilihan sekolah berdasarkan nilai rapor tertinggi. Siswa yang lolos dapat melanjutkan ke tahap daftar ulang.' 
                                    : 'Ketika masa pendaftaran berakhir, tekan tombol di samping untuk memproses ranking otomatis secara merit-based (berdasarkan nilai rata-rata rapor) dan menetapkan sekolah tujuan siswa.' }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            @if (! $isFinalized)
                                <button
                                    type="button"
                                    wire:click="finalisasiHasilSeleksi"
                                    wire:confirm="Apakah Anda yakin ingin memfinalisasi hasil seleksi? Pilihan sekolah siswa akan dikunci dan status diterima akan ditentukan."
                                    class="btn btn-primary text-white text-xs sm:text-sm font-semibold"
                                >
                                    <span wire:loading.remove wire:target="finalisasiHasilSeleksi">Umumkan & Kunci Hasil Seleksi</span>
                                    <span wire:loading wire:target="finalisasiHasilSeleksi">Memproses Hasil...</span>
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="batalkanFinalisasi"
                                    wire:confirm="Apakah Anda yakin ingin membatalkan finalisasi? Siswa akan dapat mengubah pilihan sekolah kembali."
                                    class="btn btn-outline btn-sm text-xs border-slate-300 hover:bg-slate-100 text-gray-700"
                                >
                                    <span wire:loading.remove wire:target="batalkanFinalisasi">Buka Kembali Seleksi</span>
                                    <span wire:loading wire:target="batalkanFinalisasi">Membatalkan...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Live Statistik Grid --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Total Pendaftar</span>
                        <div class="font-bold text-gray-800 text-2xl mt-1">{{ number_format($totalSiswa) }}</div>
                        <div class="text-[10px] text-gray-500 mt-1">
                            <span class="font-semibold text-emerald-600">{{ $totalTerverifikasi }}</span> Terverifikasi &bull; 
                            <span class="font-semibold text-amber-600">{{ $totalMenungguVerifikasi }}</span> Menunggu
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Akun Diaktivasi</span>
                        <div class="font-bold text-indigo-600 text-2xl mt-1">{{ number_format($totalAkunDiaktivasi) }}</div>
                        <div class="text-[10px] text-gray-500 mt-1">
                            {{ $totalDitolak > 0 ? "{$totalDitolak} berkas ditolak" : 'Siswa siap memilih sekolah' }}
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Total Sekolah & Daya Tampung</span>
                        <div class="font-bold text-blue-600 text-2xl mt-1">{{ number_format($totalKuota) }}</div>
                        <div class="text-[10px] text-gray-500 mt-1">
                            {{ $sekolahs->count() }} Sekolah ({{ $totalSma }} SMA, {{ $totalSmk }} SMK)
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-sm p-4">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase">Siswa Lolos Seleksi</span>
                        <div class="font-bold text-emerald-600 text-2xl mt-1">{{ number_format($totalDiterima) }}</div>
                        <div class="text-[10px] text-gray-500 mt-1">
                            <span class="font-semibold text-emerald-700">{{ $totalSudahDaftarUlang }}</span> sudah daftar ulang
                        </div>
                    </div>
                </div>

                {{-- Status Verifikasi Breakdown Bar --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-emerald-50/50 border border-emerald-200 rounded-sm p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-emerald-800">Berkas Terverifikasi</span>
                            <span class="badge badge-success text-white text-[10px] font-bold">{{ $totalTerverifikasi }}</span>
                        </div>
                        <p class="text-[11px] text-emerald-700 mt-1">Berkas valid dan telah diverifikasi oleh operator sekolah.</p>
                    </div>

                    <div class="bg-amber-50/50 border border-amber-200 rounded-sm p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-amber-800">Menunggu Verifikasi</span>
                            <span class="badge badge-warning text-white text-[10px] font-bold">{{ $totalMenungguVerifikasi }}</span>
                        </div>
                        <p class="text-[11px] text-amber-700 mt-1">Pengajuan pendaftaran menunggu verifikasi fisik di sekolah.</p>
                    </div>

                    <div class="bg-rose-50/50 border border-rose-200 rounded-sm p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-rose-800">Berkas Ditolak</span>
                            <span class="badge badge-error text-white text-[10px] font-bold">{{ $totalDitolak }}</span>
                        </div>
                        <p class="text-[11px] text-rose-700 mt-1">Berkas tidak memenuhi persyaratan atau data tidak cocok.</p>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <h3 class="font-bold text-gray-700 text-sm">Daftar Sekolah & Kuota Saat Ini</h3>
                    <a href="{{ route('seleksi') }}" target="_blank" class="text-xs text-blue-600 font-semibold hover:underline">
                        Lihat Jurnal Seleksi Publik &rarr;
                    </a>
                </div>

                <x-dashboard-table-admin :sekolahs="$sekolahs"/>
            </div>
        @elseif ($activeMenu === 'manage')

            @if ($isCreatingSchool || $isEditingSchool)
                <div>
                    <div class="flex flex-col mb-6">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $isEditingSchool ? 'Edit Data Sekolah' : 'Tambah Sekolah Baru' }}</h1>
                        </div>
                        <a
                            class="underline text-blue-500 cursor-pointer"
                            wire:click="closeCreateSchoolForm"
                        >
                            Kembali
                        </a>
                    </div>

                    <div class="bg-white p-6 rounded-sm border border-slate-300">
                        <form wire:submit="{{ $isEditingSchool ? 'updateSchool' : 'createSchool' }}">
                            <x-input class="max-w-sm" name="npsn_sekolah" wire:model="npsn_sekolah" legend="NPSN Sekolah" required />
                            <x-input class="max-w-sm" name="nama_sekolah" wire:model="nama_sekolah" legend="Nama Sekolah" required />
                            <x-select class="max-w-sm" name="jenis_sekolah" wire:model.live="jenis_sekolah" legend="Jenis Sekolah" :options="['SMA', 'SMK']" required />

                            @if ($jenis_sekolah === 'SMK')
                                <div class="max-w-xl p-4 border rounded-sm border-slate-300 mt-4 mb-3 bg-slate-50/50">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h3 class="font-semibold text-sm text-gray-800">Daftar Jurusan SMK</h3>
                                            <p class="text-xs text-gray-500">Wajib menambahkan minimal lebih dari 1 jurusan (minimal 2 jurusan).</p>
                                        </div>
                                        <button type="button" wire:click="addJurusan" class="btn btn-xs btn-outline btn-primary">
                                            + Tambah Jurusan
                                        </button>
                                    </div>

                                    @error('jurusans')
                                        <p class="text-xs text-red-500 mb-2 font-medium">{{ $message }}</p>
                                    @enderror

                                    <div class="space-y-2">
                                        @foreach ($jurusans as $index => $jurusan)
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1">
                                                    <x-input
                                                        wire:model="jurusans.{{ $index }}"
                                                        name="jurusans_{{ $index }}"
                                                        legend="Jurusan {{ $index + 1 }}"
                                                        placeholder="Contoh: Rekayasa Perangkat Lunak"
                                                        required
                                                    />
                                                </div>
                                                @if (count($jurusans) > 2)
                                                    <button type="button" wire:click="removeJurusan({{ $index }})" class="btn btn-sm btn-error btn-outline mt-1" title="Hapus Jurusan">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <x-input class="max-w-sm" name="nip_operator" wire:model="nip_operator" legend="NIP Operator" required />
                            <x-input class="max-w-sm" type="password" name="password_operator" wire:model="password_operator" legend="Password Operator" label="Opsional. Jika tidak diisi menggunakan password bawaan 'password'." />
                            <x-input class="max-w-sm" type="number" name="kuota_awal" wire:model="kuota_awal" legend="Kuota Awal" label="Opsional. Default 50." />
                            <button type="submit" class="btn btn-primary mt-5">
                                {{ $isEditingSchool ? 'Simpan Perubahan' : 'Tambahkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen SPMB</h1>
                    <button wire:click="openCreateSchoolForm" class="btn btn-primary mt-5">Tambah</button>
                    <x-dashboard-table-admin :sekolahs="$sekolahs"/>
                </div>
            @endif
        @endif
    </main>
</div>
