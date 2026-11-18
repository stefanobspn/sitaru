<div>
    <div class="flex justify-center flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="10em" height="10em" viewBox="0 0 24 24">
            <path d="M0 0h24v24H0z" fill="none" />
            <path class="fill-green-500" d="M9 16.17L4.83 12l-1.42 1.41L9 19L21 7l-1.41-1.41z" />
        </svg>
        <h1 class="text-green-500 text-xl font-bold">Sukses!</h1>
        @if ($registeredSiswa)
            <div class="mt-2 text-center text-sm text-base-content/80">
                <p>Nomor Pendaftaran: <span class="font-bold text-base-content">{{ $registeredSiswa->nomor_pendaftaran }}</span></p>
                <p>Nama: <span class="font-semibold text-base-content">{{ $registeredSiswa->nama_lengkap }}</span></p>
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
                class="alert alert-info mt-4 py-2 px-4 text-sm max-w-md"
            >
                {{ session('info') }}
            </div>
        @endif
        <button wire:click="cetakBuktiPendaftaran" class="btn btn-primary mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3m-3 11H8v-5h8zm3-7c-.55 0-1-.45-1-1s.45-1 1-1s1 .45 1 1s-.45 1-1 1m-1-9H6v4h12z" />
            </svg>
            Cetak Bukti
        </button>
        <a class="mt-4 text-blue-500 underline" href="/">Kembali ke Beranda</a>
    </div>
</div>
