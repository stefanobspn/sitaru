<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto">
        {{-- Header Card --}}
        <div class="mb-6 text-center">
            <a href="{{ route('index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline mb-2">
                Kembali ke Beranda
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Aktivasi Akun Siswa</h1>
            <p class="text-sm text-gray-600 mt-1">
                Masukkan kode aktivasi dari berkas verifikasi.
            </p>
        </div>

        {{-- Banner Jadwal Aktivasi --}}
        <div class="p-4 mb-6 rounded-sm border {{ $isAktivasiOpen ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($aktivasiStatus === 'not_started' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-slate-100 border-slate-300 text-slate-800') }} text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="font-bold">Jadwal Agenda Aktivasi Akun:</span>
                <span>4 - 13 Juni 2026</span>
            </div>
            <div>
                @if ($isAktivasiOpen)
                    <span class="badge badge-success text-white font-semibold text-[11px]">Jadwal Berlangsung</span>
                @elseif ($aktivasiStatus === 'not_started')
                    <span class="badge badge-warning text-white font-semibold text-[11px]">Belum Dibuka (Mulai 4 Juni 2026)</span>
                @else
                    <span class="badge badge-neutral text-white font-semibold text-[11px]">Telah Ditutup (Selesai 13 Juni 2026)</span>
                @endif
            </div>
        </div>

        @if (session()->has('error'))
            <div role="alert" class="alert alert-error text-white text-sm py-3 px-4 rounded-sm mb-6">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Step Indicator --}}
        <div class="grid grid-cols-2 gap-2 mb-6">
            <div class="p-3 border-b-2 {{ $step === 1 ? 'border-blue-600 bg-white font-semibold text-blue-600' : 'border-slate-300 bg-slate-100 text-gray-500' }} text-center text-xs sm:text-sm rounded-t-sm">
                1. Verifikasi Kode
            </div>
            <div class="p-3 border-b-2 {{ $step === 2 ? 'border-blue-600 bg-white font-semibold text-blue-600' : 'border-slate-300 bg-slate-100 text-gray-500' }} text-center text-xs sm:text-sm rounded-t-sm">
                2. Buat Password
            </div>
        </div>

        @if ($step === 1)
            {{-- Form Step 1: Cek Kode Aktivasi --}}
            <div class="bg-white border border-slate-300 rounded-sm p-6 md:p-8">
                @if (! $isAktivasiOpen)
                    <div class="alert {{ $aktivasiStatus === 'closed' ? 'alert-warning' : 'alert-info text-white' }} text-xs py-3 px-4 rounded-sm mb-4">
                        <span>{{ $agendaErrorMessage }}</span>
                    </div>
                @endif
                <form wire:submit="cekKodeAktivasi" class="space-y-4">
                    <x-input
                        name="nisn"
                        wire:model="nisn"
                        legend="NISN (Nomor Induk Siswa Nasional)"
                        placeholder="10 digit NISN Anda"
                        required
                    />

                    <x-input
                        name="activation_code"
                        wire:model="activation_code"
                        legend="Kode Aktivasi (Activation Code)"
                        placeholder="Contoh: ACT-A1B2C3D4"
                        label="Kode ini tercantum pada lembar bukti verifikasi dari Operator."
                        required
                    />

                    <div class="pt-4">
                        <button
                            type="submit"
                            class="btn btn-primary w-full text-white font-semibold"
                            @disabled(! $isAktivasiOpen)
                        >
                            <span wire:loading.remove wire:target="cekKodeAktivasi">Verifikasi Kode & Lanjutkan</span>
                            <span wire:loading wire:target="cekKodeAktivasi">Memeriksa Data...</span>
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200 text-center text-xs text-gray-600">
                    Sudah pernah melakukan aktivasi akun?
                    <a href="{{ route('siswa') }}" class="text-blue-600 font-semibold hover:underline">
                        Masuk ke Akun Siswa
                    </a>
                </div>
            </div>
        @else
            {{-- Form Step 2: Buat Password --}}
            <div class="bg-white border border-slate-300 rounded-sm p-6 md:p-8">
                {{-- Data Siswa Terverifikasi --}}
                <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Data Calon Siswa</span>
                        <span class="badge badge-success text-white text-xs">Terverifikasi</span>
                    </div>
                    <div class="font-bold text-gray-800 text-base">{{ $siswa?->nama_lengkap }}</div>
                    <div class="text-xs text-gray-600 mt-1">
                        <span>NISN: <strong>{{ $siswa?->nisn }}</strong></span> &bull;
                        <span>Asal: <strong>{{ $siswa?->nama_sekolah_asal }}</strong></span>
                    </div>
                </div>

                <form wire:submit="simpanAktivasi" class="space-y-4">
                    <x-input
                        name="password"
                        type="password"
                        wire:model="password"
                        legend="Password Baru"
                        placeholder="Minimal 8 karakter"
                        required
                    />

                    <x-input
                        name="password_confirmation"
                        type="password"
                        wire:model="password_confirmation"
                        legend="Konfirmasi Password Baru"
                        placeholder="Ulangi password baru"
                        required
                    />

                    <div class="pt-4 flex flex-col sm:flex-row gap-2">
                        <button
                            type="button"
                            wire:click="kembaliKeStep1"
                            class="btn btn-ghost border border-slate-300 order-2 sm:order-1"
                        >
                            Kembali
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary flex-1 text-white font-semibold order-1 sm:order-2"
                        >
                            <span wire:loading.remove wire:target="simpanAktivasi">Simpan & Aktifkan Akun</span>
                            <span wire:loading wire:target="simpanAktivasi">Menyimpan Akun...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
