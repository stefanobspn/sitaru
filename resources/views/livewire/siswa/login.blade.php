<div class="flex flex-col items-center justify-center min-h-screen bg-slate-50 px-4">
    <div class="mb-4 text-center">
        <a href="{{ route('index') }}" class="text-xs font-semibold text-blue-600 hover:underline">
            Kembali ke Beranda
        </a>
    </div>

    <div class="border border-slate-300 w-full max-w-md p-6 sm:p-8 rounded-lg bg-white">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Login Calon Siswa</h1>
            <p class="text-xs text-gray-600 mt-1">
                Gunakan NISN dan Password yang telah Anda buat saat aktivasi akun.
            </p>
        </div>

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

        <form wire:submit="login" class="space-y-4">
            <x-input
                name="nisn"
                wire:model="nisn"
                type="text"
                legend="NISN (10 Digit)"
                placeholder="Contoh: 0012345678"
                required
            />

            <x-input
                name="password"
                wire:model="password"
                type="password"
                legend="Password"
                placeholder="Password akun Anda"
                required
            />

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none" for="remember">
                    <input type="checkbox" id="remember" name="remember" wire:model="remember" class="checkbox checkbox-primary checkbox-xs" />
                    <span class="text-gray-700">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-full text-white font-semibold mt-2">
                <span wire:loading.remove wire:target="login">Masuk ke Akun Siswa</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200 text-xs text-gray-600 space-y-3">
            <div class="bg-blue-50 border border-blue-200 rounded-sm p-3 text-left">
                <span class="font-semibold text-blue-900 block mb-0.5">Lupa Password Akun?</span>
                <p class="text-blue-800 leading-relaxed text-[11px]">
                    Jika Anda lupa password akun siswa, silakan hubungi atau datang langsung ke <strong>Operator Sekolah yang memverifikasi berkas pendaftaran Anda</strong> untuk melakukan permohonan reset password.
                </p>
            </div>

            <div class="text-center space-y-1.5 pt-1">
                <p>
                    Belum melakukan aktivasi akun?
                    <a href="{{ route('aktivasi') }}" class="text-blue-600 font-semibold hover:underline">
                        Aktivasi di sini
                    </a>
                </p>
                <p>
                    Belum memiliki akun pendaftaran?
                    <a href="{{ route('daftar') }}" class="text-blue-600 font-semibold hover:underline">
                        Ajukan akun pendaftaran
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

