@php
    use App\Enums\WilayahSekolahAsal;
    use App\Enums\TahunLulus;

    $fields = [
        'nama_lengkap' => 'Nama Lengkap',
        'nama_sekolah_asal' => 'Nama Sekolah Asal',
        'tempat_lahir' => 'Tempat Lahir',
        'tanggal_lahir' => 'Tanggal Lahir',
        'alamat' => 'Alamat',
        'provinsi' => 'Provinsi',
        'kabupaten_kota' => 'Kabupaten/Kota',
        'kecamatan' => 'Kecamatan',
        'kelurahan' => 'Kelurahan',
        'rt' => 'RT',
        'rw' => 'RW'
    ];

@endphp


<div>
    @if ($currentSubStep === 1)
        <x-input wire:model="biodata.nisn" name="nisn" legend="NISN" required />
        <x-input wire:model="biodata.nik" name="nik" legend="NIK" required />

        <x-select wire:model="biodata.tahunLulus" name="tahun_lulus" legend="Tahun Lulus" :options="TahunLulus::values()" required />
        <x-select wire:model="biodata.wilayahSekolahAsal" name="wilayah_sekolah_asal" legend="Wilayah Sekolah Asal" :options="WilayahSekolahAsal::values()"
                  required />

        <button wire:click="searchBiodata" class="btn btn-primary w-full mt-4">Cari</button>
        @error('biodata.searchBiodataError')
            <div class="text-error text-md text-center mt-2">Data tidak ditemukan</div>
        @enderror
    @elseif ($currentSubStep === 2)
        <div class="flow-root mb-8">
            <dl class="-my-3 divide-y divide-gray-200 text-sm">
                @foreach ($fields as $key => $label)
                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3 sm:gap-4">
                        <dt class="font-medium text-gray-900">{{ $label }}</dt>
                        <dd class="text-gray-700 lg:text-right sm:col-span-2">
                            {{ $this->biodata->biodataDitemukan[$key] ?? '-' }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <button wire:click="nextStep" class="btn btn-primary w-full mt-4">Konfirmasi</button>

    @endif

</div>
