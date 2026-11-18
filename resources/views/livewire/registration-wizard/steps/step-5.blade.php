@assets
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endassets

@php
    use App\Enums\StatusDomisili;
    use App\Enums\StatusPrestasi;
    use App\Enums\KategoriEventPrestasi;
    use App\Enums\StatusKepengurusanOrganisasi;
    use Carbon\Carbon;

    $siswa = $biodata->biodataDitemukan;
@endphp

<div class="max-w-7xl mx-auto space-y-6 p-4 sm:p-6">

    {{-- Top Row: Biodata & Kontak Domisili --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Card 1: Biodata Diri --}}
        <div class="bg-base-100 border border-slate-300 rounded-sm p-6">
            <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-200">
                <h2 class="text-lg font-bold text-base-content">Biodata Diri</h2>
            </div>

            <dl class="divide-y divide-base-200 text-sm">
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">NISN</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $biodata->nisn ?: '-' }}</dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">NIK</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $biodata->nik ?: '-' }}</dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Nama Lengkap</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $siswa['nama_lengkap'] ?? '-' }}</dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Sekolah Asal</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $siswa['nama_sekolah_asal'] ?? '-' }}</dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Tahun Lulus</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $biodata->tahunLulus ?: '-' }}</dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Tempat, Tanggal Lahir</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">
                        {{ $siswa['tempat_lahir'] ?? '-' }},
                        {{ isset($siswa['tanggal_lahir']) ? Carbon::parse($siswa['tanggal_lahir'])->translatedFormat('d F Y') : '-' }}
                    </dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Alamat</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $siswa['alamat'] ?? '-' }}</dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">RT / RW</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">
                        {{ $siswa['rt'] ?? '-' }} / {{ $siswa['rw'] ?? '-' }}
                    </dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Kelurahan / Kecamatan</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">
                        {{ $siswa['kelurahan'] ?? '-' }}, {{ $siswa['kecamatan'] ?? '-' }}
                    </dd>
                </div>
                <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <dt class="font-medium text-base-content/70">Kabupaten / Kota</dt>
                    <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $siswa['kabupaten_kota'] ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Card 2: Kontak, Domisili & Map --}}
        <div class="bg-base-100 border border-slate-300 rounded-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-base-content">Kontak & Domisili</h2>
                </div>

                <dl class="divide-y divide-base-200 text-sm">
                    <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="font-medium text-base-content/70">Nomor WhatsApp</dt>
                        <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->noWhatsapp ?: '-' }}</dd>
                    </div>
                    <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="font-medium text-base-content/70">Email</dt>
                        <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->email ?: '-' }}</dd>
                    </div>
                    <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="font-medium text-base-content/70">Status Domisili</dt>
                        <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->statusDomisili ?: '-' }}</dd>
                    </div>

                    @if($kontakDomisili->statusDomisili === StatusDomisili::SesuaiKK->value)
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Tanggal Cetak KK</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">
                                {{ $kontakDomisili->tglCetakKk ? Carbon::parse($kontakDomisili->tglCetakKk)->translatedFormat('d F Y') : '-' }}
                            </dd>
                        </div>
                    @elseif($kontakDomisili->statusDomisili === StatusDomisili::SesuaiMutasiOrtuWali->value)
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Instansi Penugasan</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->namaInstansiMutasi ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">No. Surat Penugasan</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->nomorSuratPenugasan ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Tgl. Surat Penugasan</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">
                                {{ $kontakDomisili->tglSuratPenugasan ? Carbon::parse($kontakDomisili->tglSuratPenugasan)->translatedFormat('d F Y') : '-' }}
                            </dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">No. SK Domisili Desa</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->nomorSkDomisili ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Anak Guru / Tendik</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->isAnakGuru ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                    @elseif($kontakDomisili->statusDomisili === StatusDomisili::SesuaiDomisiliPondok->value)
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Pondok Pesantren</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->namaPondokPesantren ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">No. Izin EMIS/NSPP</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->nomorIzinPondokEmis ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Jenjang Pondok</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->jenjangPondok ?: '-' }}</dd>
                        </div>
                    @elseif($kontakDomisili->statusDomisili === StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Panti Asuhan</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->namaPantiAsuhan ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">No. Registrasi Dinsos</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->nomorRegistrasiDinsos ?: '-' }}</dd>
                        </div>
                        <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="font-medium text-base-content/70">Prioritas Panti</dt>
                            <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2">{{ $kontakDomisili->prioritasPanti ?: '-' }}</dd>
                        </div>
                    @endif

                    <div class="py-2.5 grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="font-medium text-base-content/70">Koordinat Rumah</dt>
                        <dd class="font-semibold text-base-content text-left sm:text-right sm:col-span-2 font-mono text-xs">
                            {{ $kontakDomisili->pastedCoordinate ?: '-' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Map Preview (Leaflet) --}}
            <div
                wire:ignore
                x-data="{
                    init() {
                        const lat = {{ $kontakDomisili->latitude ?? 'null' }};
                        const lng = {{ $kontakDomisili->longitude ?? 'null' }};
                        if (lat && lng && typeof L !== 'undefined') {
                            const map = L.map($refs.mapPreview, {
                                dragging: false,
                                zoomControl: false,
                                scrollWheelZoom: false,
                                doubleClickZoom: false,
                                touchZoom: false,
                                keyboard: false
                            }).setView([lat, lng], 17);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(map);

                            L.marker([lat, lng]).addTo(map);
                            setTimeout(() => map.invalidateSize(), 200);
                        }
                    }
                }"
                class="mt-5 pt-4 border-slate-200"
            >
                <span class="text-xs font-semibold tracking-wider text-base-content/60 block mb-2">
                    Preview Map
                </span>
                <div x-ref="mapPreview" class="w-full h-44 bg-base-200 rounded-sm border border-slate-300 z-0"></div>
            </div>
        </div>
    </div>

    {{-- Card 3: Akademik & Prestasi --}}
    <div class="bg-base-100 border border-slate-300 rounded-sm p-6">
        <div class="pb-4 mb-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-base-content">Akademik & Prestasi</h2>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            {{-- Left: Nilai Rapor Table --}}
            <div class="xl:col-span-7">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-base-content">Nilai Rapor Semester 1 s.d 5</h3>
                    @php
                        $step5Nrtka = (float) ($siswa['nrtka'] ?? 0);
                        $step5Na = $akademikPrestasi->hitungNilaiAkhir($step5Nrtka);
                    @endphp
                </div>
                <div class="overflow-x-auto border border-slate-300 rounded-sm">
                    <table class="table table-zebra table-sm w-full">
                        <thead class="bg-base-200/70 text-base-content">
                        <tr>
                            <th class="w-10">#</th>
                            <th>Mata Pelajaran</th>
                            @for ($i = 1; $i <= 5; $i++)
                                <th class="text-center">SEM {{ $i }}</th>
                            @endfor
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($akademikPrestasi::DAFTAR_MAPEL as $key => $namaMapel)
                            <tr>
                                <td class="font-medium text-base-content/60">{{ $loop->iteration }}</td>
                                <td class="font-medium">{{ $namaMapel }}</td>
                                @for ($sem = 1; $sem <= 5; $sem++)
                                    <td class="text-center font-semibold text-slate-800">
                                        {{ $akademikPrestasi->nilaiRapor[$key][$sem] ?? '-' }}
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right: Detail Status, Prestasi & Organisasi --}}
            <div class="xl:col-span-5">
                <h3 class="text-sm font-semibold text-base-content mb-3">Detail Status & Prestasi</h3>
                <div class="bg-base-200/40 rounded-sm p-4 border border-slate-300">
                    <dl class="divide-y divide-base-200 text-sm">
                        <div class="py-2 flex justify-between gap-2">
                            <dt class="text-base-content/70">Status Khusus</dt>
                            <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->statusKhusus ?: '-' }}</dd>
                        </div>
                        <div class="py-2 flex justify-between gap-2">
                            <dt class="text-base-content/70">Status Prestasi</dt>
                            <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->statusPrestasi ?: '-' }}</dd>
                        </div>

                        {{-- Tampilkan hanya jika memiliki prestasi --}}
                        @if($akademikPrestasi->statusPrestasi === StatusPrestasi::Memiliki->value)
                            <div class="py-2 flex justify-between gap-2">
                                <dt class="text-base-content/70">Kategori Event</dt>
                                <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->kategoriEventPrestasi ?: '-' }}</dd>
                            </div>

                            @if($akademikPrestasi->kategoriEventPrestasi === KategoriEventPrestasi::Berjenjang->value)
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Tingkat Kejuaraan</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->tingkatKejuaraanBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Nama Kejuaraan</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->namaKejuaraanBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Peringkat Juara</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->peringkatJuaraBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Tahun Perolehan</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->tahunPerolehanKejuaraanBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Penyelenggara</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->penyelenggaraKejuaraanBerjenjang ?: '-' }}</dd>
                                </div>
                            @elseif($akademikPrestasi->kategoriEventPrestasi === KategoriEventPrestasi::TidakBerjenjang->value)
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Tingkat Kejuaraan</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->tingkatKejuaraanTidakBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Peringkat Kurasi</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->peringkatKurasiTidakBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Nama Kejuaraan</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->namaKejuaraanTidakBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Peringkat Juara</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->peringkatJuaraTidakBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Tahun Perolehan</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->tahunPerolehanKejuaraanTidakBerjenjang ?: '-' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between gap-2">
                                    <dt class="text-base-content/70">Penyelenggara</dt>
                                    <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->penyelenggaraKejuaraanTidakBerjenjang ?: '-' }}</dd>
                                </div>
                            @endif
                        @endif

                        <div class="py-2 flex justify-between gap-2">
                            <dt class="text-base-content/70">Kepengurusan Organisasi</dt>
                            <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->statusKepengurusanOrganisasi ?: '-' }}</dd>
                        </div>

                        {{-- Tampilkan hanya jika pernah mengurus --}}
                        @if($akademikPrestasi->statusKepengurusanOrganisasi === StatusKepengurusanOrganisasi::PernahMengurus->value)
                            <div class="py-2 flex justify-between gap-2">
                                <dt class="text-base-content/70">Jabatan & Organisasi</dt>
                                <dd class="font-semibold text-base-content text-right">{{ $akademikPrestasi->jenisOrganisasiJabatan ?: '-' }}</dd>
                            </div>
                        @endif

                        <div class="py-2 flex justify-between gap-2 border-t border-slate-200">
                            <dt class="font-medium text-base-content/80">Nilai Prestasi/Kejuaraan (NK)</dt>
                            <dd class="font-bold text-blue-700 text-right">{{ $akademikPrestasi->getFormattedNK() }}</dd>
                        </div>
                        <div class="py-2 flex justify-between gap-2">
                            <dt class="font-medium text-base-content/80">Nilai Organisasi (NO)</dt>
                            <dd class="font-bold text-blue-700 text-right">{{ $akademikPrestasi->getFormattedNO() }}</dd>
                        </div>
                        <div class="py-2 flex justify-between gap-2">
                            <dt class="font-medium text-base-content/80">Nilai TKA (NRTKA)</dt>
                            <dd class="font-bold text-emerald-700 text-right">{{ number_format($step5Nrtka, 2) }}</dd>
                        </div>
                        <div class="py-2 flex justify-between gap-2 border-t border-slate-200">
                            <dt class="font-bold text-base-content">Nilai Akhir (NA)</dt>
                            <dd class="font-bold text-primary text-right">
                                {{ is_numeric($step5Na) ? number_format($step5Na, 2) : $step5Na }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Berkas yang Diunggah --}}
    @php
        $berkasList = [
            'Surat Pernyataan Kebenaran' => $berkas->suratPernyataanKebenaran,
            'Kartu Keluarga' => $berkas->kartuKeluarga,
            'Surat Keterangan Nilai Rapor (Sem 1-5)' => $berkas->suratKeteranganNilaiRapor,
        ];

        if ($akademikPrestasi->statusPrestasi === StatusPrestasi::Memiliki->value && $berkas->piagamPrestasi) {
            $berkasList['Piagam Prestasi / Kejuaraan'] = $berkas->piagamPrestasi;
        }

        if ($akademikPrestasi->statusKepengurusanOrganisasi === StatusKepengurusanOrganisasi::PernahMengurus->value && $berkas->skOrganisasi) {
            $berkasList['Surat Keterangan Pengurus Organisasi'] = $berkas->skOrganisasi;
        }
    @endphp

    <div class="bg-base-100 border border-slate-300 rounded-sm p-6">
        <div class="pb-4 mb-4 border-slate-200">
            <h2 class="text-lg font-bold text-base-content">Berkas yang Diunggah</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($berkasList as $label => $file)
                @if ($file)
                    <div class="flex items-center justify-between p-3.5 rounded-sm border border-slate-300 bg-slate-50">
                        <div class="flex flex-col min-w-0 pr-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase">{{ $label }}</span>
                            <span class="text-sm font-medium text-slate-900 truncate" title="{{ $file->getClientOriginalName() }}">
                                {{ $file->getClientOriginalName() }}
                            </span>
                            <span class="text-xs text-slate-400 mt-0.5">
                                {{ round($file->getSize() / 1024, 1) }} KB
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Bottom Action Bar: Pernyataan & Submit --}}
    <div class="bg-primary/5 border border-primary/20 rounded-sm p-5 flex flex-col gap-3">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <label class="cursor-pointer flex items-start gap-3" for="pernyataan_setuju">
                <input type="checkbox" id="pernyataan_setuju" name="pernyataan_setuju" wire:model="pernyataanSetuju" class="checkbox checkbox-primary mt-0.5" required />
                <span class="text-sm text-base-content leading-snug">
                    Saya menyatakan bahwa semua data yang saya isi dan berkas yang saya unggah adalah benar dan dapat dipertanggungjawabkan.
                </span>
            </label>

            <button wire:click="submitPendaftaran" class="btn btn-primary w-full sm:w-auto px-8 shrink-0">
                Daftar Sekarang
            </button>
        </div>

        @error('pernyataanSetuju')
            <p class="text-error text-sm font-medium">{{ $message }}</p>
        @enderror
    </div>

</div>
