@php
    use App\Livewire\Forms\AkademikPrestasiSiswaForm;
    use App\Enums\StatusKhusus;
    use App\Enums\StatusPrestasi;
    use App\Enums\KategoriEventPrestasi;
    use App\Enums\TingkatKejuaraanPrestasiBerjenjang;
    use App\Enums\NamaKejuaraanPrestasiBerjenjang;
    use App\Enums\PeringkatJuaraBerjenjang;
    use App\Enums\TahunPerolehanKejuaraanBerjenjang;
    use App\Enums\PenyelenggaraKejuaraanBerjenjang;
    use App\Enums\PeringkatKurasiTidakBerjenjang;
    use App\Enums\StatusKepengurusanOrganisasi;
    use App\Enums\JenisOrganisasiJabatan;
@endphp

<div>
    @if ($currentSubStep === 1)
        <div
            x-data="{
                hitungNRR() {
                    const inputs = $el.querySelectorAll('.input-nilai-rapor');
                    let total = 0;
                    let count = 0;
                    inputs.forEach(input => {
                        const val = parseFloat(input.value);
                        if (!isNaN(val) && val >= 0 && val <= 100) {
                            total += val;
                            count++;
                        }
                    });
                    return count > 0 ? (total / count).toFixed(2) : '0.00';
                },
                nrr: '{{ number_format($akademikPrestasi->hitungRataRata(), 2) }}'
            }"
            @input="nrr = hitungNRR()"
        >
            <h1 class="mb-5 text-xl font-semibold">Nilai Rapor Semester 1-5</h1>

            <div class="overflow-x-auto border border-base-content/5 bg-base-100">
                <table class="table">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">Nama</th>
                        @for ($i = 1; $i <= 5; $i++)
                            <th class="text-center">SEM {{ $i }}</th>
                        @endfor
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($akademikPrestasi::DAFTAR_MAPEL as $key => $namaMapel)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $namaMapel }}</td>
                            @for ($sem = 1; $sem <= 5; $sem++)
                                @php
                                    $errorField = "akademikPrestasi.nilaiRapor.{$key}.{$sem}";
                                    $isError = $errors->has($errorField);
                                @endphp

                                <td class="text-center">
                                    <input
                                        id="nilai_rapor_{{ $key }}_{{ $sem }}"
                                        name="nilai_rapor_{{ $key }}_{{ $sem }}"
                                        wire:model.blur="akademikPrestasi.nilaiRapor.{{ $key }}.{{ $sem }}"
                                        type="text"
                                        inputmode="decimal"
                                        placeholder="00.00"
                                        aria-label="{{ $namaMapel }} Semester {{ $sem }}"
                                        onkeydown="
                                                if (event.ctrlKey || event.metaKey || ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(event.key)) return;
                                                const nextVal = this.value.slice(0, this.selectionStart) + event.key + this.value.slice(this.selectionEnd);
                                                if (!/^(100(\.0{0,2})?|[0-9]{1,2}(\.[0-9]{0,2})?)$/.test(nextVal)) event.preventDefault();
                                            "
                                        @if ($isError)
                                            title="{{ $errors->first($errorField) }}"
                                        @endif
                                        class="input input-sm w-20 text-center input-nilai-rapor {{ $isError ? 'input-error border-red-500 text-red-600' : 'border-slate-300' }}"
                                    >
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex w-full justify-between items-center bg-slate-50 border border-slate-200 p-4 rounded-sm">
                <div class="text-sm sm:text-base font-semibold text-gray-800">
                    Nilai Rata-Rata Rapor (NRR) :
                    <span x-text="nrr">
                        {{ number_format($akademikPrestasi->hitungRataRata(), 2) }}
                    </span>
                </div>
                <button wire:click="submitRaport" class="btn btn-primary">Lanjutkan</button>
            </div>
        </div>

    @elseif ($currentSubStep === 2)
        <x-select wire:model="akademikPrestasi.statusKhusus" name="status_khusus" legend="Status Khusus"
                  :options="StatusKhusus::values()" required />
        <x-select wire:model.live="akademikPrestasi.statusPrestasi" name="status_prestasi" legend="Status Prestasi"
                  :options="StatusPrestasi::values()" required />

        @if ($akademikPrestasi->statusPrestasi === StatusPrestasi::Memiliki->value)
            <div class="p-4 border rounded-sm border-slate-300 mt-2">
                <x-select wire:model.live="akademikPrestasi.kategoriEventPrestasi" name="kategori_event_prestasi"
                          legend="Kategori Event Prestasi" :options="KategoriEventPrestasi::values()" required />

                @if ($akademikPrestasi->kategoriEventPrestasi === KategoriEventPrestasi::Berjenjang->value)
                    <x-select wire:model.live="akademikPrestasi.tingkatKejuaraanBerjenjang" name="tingkat_kejuaraan_berjenjang"
                              legend="Tingkat Kejuaraan" :options="TingkatKejuaraanPrestasiBerjenjang::values()" required />
                    <x-select wire:model="akademikPrestasi.namaKejuaraanBerjenjang" name="nama_kejuaraan_berjenjang" legend="Nama Kejuaraan"
                              :options="$akademikPrestasi->getDaftarNamaKejuaraanBerjenjangOptions()" required />
                    <x-select wire:model.live="akademikPrestasi.peringkatJuaraBerjenjang" name="peringkat_juara_berjenjang"
                              legend="Peringkat Juara" :options="PeringkatJuaraBerjenjang::values()" required />
                    <x-select wire:model="akademikPrestasi.tahunPerolehanKejuaraanBerjenjang" name="tahun_perolehan_kejuaraan_berjenjang"
                              legend="Tahun Perolehan" :options="TahunPerolehanKejuaraanBerjenjang::values()" required />
                    <x-select wire:model="akademikPrestasi.penyelenggaraKejuaraanBerjenjang" name="penyelenggara_kejuaraan_berjenjang"
                              legend="Penyelenggara" :options="PenyelenggaraKejuaraanBerjenjang::values()" required />
                @elseif ($akademikPrestasi->kategoriEventPrestasi === KategoriEventPrestasi::TidakBerjenjang->value)
                    <x-select wire:model.live="akademikPrestasi.tingkatKejuaraanTidakBerjenjang" name="tingkat_kejuaraan_tidak_berjenjang"
                              legend="Tingkat Kejuaraan" :options="TingkatKejuaraanPrestasiBerjenjang::values()" required />
                    <x-select wire:model.live="akademikPrestasi.peringkatKurasiTidakBerjenjang" name="peringkat_kurasi_tidak_berjenjang"
                              legend="Peringkat Kurasi Talenta" :options="PeringkatKurasiTidakBerjenjang::values()" required />
                    <x-input wire:model="akademikPrestasi.namaKejuaraanTidakBerjenjang" name="nama_kejuaraan_tidak_berjenjang"
                             legend="Nama Kejuaraan" placeholder="Nama Kejuaraan / Perlombaan" required />
                    <x-select wire:model.live="akademikPrestasi.peringkatJuaraTidakBerjenjang" name="peringkat_juara_tidak_berjenjang"
                              legend="Peringkat Juara" :options="PeringkatJuaraBerjenjang::values()" required />
                    <x-select wire:model="akademikPrestasi.tahunPerolehanKejuaraanTidakBerjenjang" name="tahun_perolehan_kejuaraan_tidak_berjenjang"
                              legend="Tahun Perolehan" :options="TahunPerolehanKejuaraanBerjenjang::values()" required />
                    <x-input wire:model="akademikPrestasi.penyelenggaraKejuaraanTidakBerjenjang" name="penyelenggara_kejuaraan_tidak_berjenjang"
                             legend="Penyelenggara" placeholder="Instansi / Lembaga Penyelenggara" required />
                @endif
            </div>
        @endif

        <x-select wire:model.live="akademikPrestasi.statusKepengurusanOrganisasi" name="status_kepengurusan_organisasi" legend="Status Kepengurusan Organisasi"
                  :options="StatusKepengurusanOrganisasi::values()" required />

        @if ($akademikPrestasi->statusKepengurusanOrganisasi === StatusKepengurusanOrganisasi::PernahMengurus->value)
            <div class="p-4 border rounded-sm border-slate-300 mt-2">
                <x-select wire:model.live="akademikPrestasi.jenisOrganisasiJabatan" name="jenis_organisasi_jabatan"
                          legend="Jenis Organisasi & Jabatan" :options="JenisOrganisasiJabatan::values()" required />
            </div>
        @endif

        <div class="mt-5 space-y-1 text-sm bg-slate-50 border border-slate-200 p-4 rounded-sm">
            <span class="block">Nilai Prestasi/Kejuaraan (NK): {{ $akademikPrestasi->getFormattedNK() }}</span>
            <span class="block">Nilai Organisasi (NO): {{ $akademikPrestasi->getFormattedNO() }}</span>
            @php
                $nrtka = (float) ($biodata->biodataDitemukan['nrtka'] ?? 0);
                $na = $akademikPrestasi->hitungNilaiAkhir($nrtka);
            @endphp
            <span class="block">Nilai Tes Kemampuan Akademik (NRTKA): {{ number_format($nrtka, 2) }}</span>
            <span class="block text-base font-bold text-primary mt-2">
                Estimasi Nilai Akhir (NA): {{ is_numeric($na) ? number_format($na, 2) : $na }}
            </span>
        </div>

        <button wire:click="submitPrestasi" class="btn btn-primary w-full mt-4">Lanjutkan</button>
    @endif
</div>
