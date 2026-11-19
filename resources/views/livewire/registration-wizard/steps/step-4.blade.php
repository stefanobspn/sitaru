<div>
    <h1 class="text-xl font-bold text-slate-900">Unggah Berkas Persyaratan</h1>
    <p class="text-sm text-slate-500 mt-1">
        Format berkas harus berupa <span class="font-semibold text-slate-700">PDF</span> dengan ukuran maksimal <span class="font-semibold text-slate-700">1 MB</span> per dokumen.
    </p>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
        {{-- wajib --}}
        <x-file-uploaders wire:model="berkas.suratPernyataanKebenaran" legend="Surat Pernyataan Kebenaran" required/>
        <x-file-uploaders wire:model="berkas.kartuKeluarga" legend="Kartu Keluarga" required/>
        <x-file-uploaders wire:model="berkas.suratKeteranganNilaiRapor" legend="Surat Keterangan Nilai Rapor Semester 1 s.d 5" required/>

        {{-- conditional --}}
        @if($akademikPrestasi->requiresPiagamPrestasi())
            <x-file-uploaders wire:model="berkas.piagamPrestasi" legend="Piagam Prestasi / Kejuaraan" required/>
        @endif

        @if($akademikPrestasi->requiresSkOrganisasi())
            <x-file-uploaders wire:model="berkas.skOrganisasi" legend="Surat Keterangan Pengurus Organisasi" required/>
        @endif
    </div>

    <div class="mt-6 flex justify-stretch sm:justify-end">
        <button wire:click="submitBerkas" class="btn btn-primary w-full sm:w-auto">Lanjutkan</button>
    </div>
</div>
