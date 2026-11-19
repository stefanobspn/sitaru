@php
    use App\Enums\StatusDomisili;
@endphp

<div>
    @if($currentSubStep === 1)
        <x-input wire:model="kontakDomisili.noWhatsapp" name="no_whatsapp" legend="Nomor WhatsApp" required />
        <x-input wire:model="kontakDomisili.email" name="email" legend="Email" type="email" required />
        <x-select wire:model.live="kontakDomisili.statusDomisili" name="status_domisili" legend="Status Domisili" :options="StatusDomisili::values()" required />

        @if($kontakDomisili->statusDomisili === StatusDomisili::SesuaiKK->value)
            <div class="mt-4 p-4 border border-slate-200 rounded-sm bg-base-100 space-y-3">
                <h3 class="font-bold text-sm text-base-content">Detail Kartu Keluarga (KK)</h3>
                <x-input wire:model="kontakDomisili.tglCetakKk" name="tgl_cetak_kk" legend="Tanggal Cetak KK" type="date" required />
            </div>
        @elseif($kontakDomisili->statusDomisili === StatusDomisili::SesuaiMutasiOrtuWali->value)
            <div class="mt-4 p-4 border border-slate-200 rounded-sm bg-base-100 space-y-3">
                <x-input wire:model="kontakDomisili.namaInstansiMutasi" name="nama_instansi_mutasi" legend="Nama Instansi/Kantor/Perusahaan Penugasan" required />
                <x-input wire:model="kontakDomisili.nomorSuratPenugasan" name="nomor_surat_penugasan" legend="Nomor Surat Penugasan / SK Mutasi" required />
                <x-input wire:model="kontakDomisili.tglSuratPenugasan" name="tgl_surat_penugasan" legend="Tanggal Surat Penugasan" type="date" required />
                <x-input wire:model="kontakDomisili.nomorSkDomisili" name="nomor_sk_domisili" legend="Nomor SK Domisili Kelurahan/Desa" required />

                <div class="form-control mt-2">
                    <label class="label cursor-pointer justify-start gap-3" for="is_anak_guru">
                        <input type="checkbox" id="is_anak_guru" name="is_anak_guru" wire:model="kontakDomisili.isAnakGuru" class="checkbox checkbox-primary" />
                        <span class="label-text font-medium text-wrap">Calon siswa merupakan anak Guru/Tenaga Kependidikan</span>
                    </label>
                </div>
            </div>
        @elseif($kontakDomisili->statusDomisili === StatusDomisili::SesuaiDomisiliPondok->value)
            <div class="mt-4 p-4 border border-slate-200 rounded-sm bg-base-100 space-y-3">
                <x-input wire:model="kontakDomisili.namaPondokPesantren" name="nama_pondok_pesantren" legend="Nama Pondok Pesantren" required />
                <x-input wire:model="kontakDomisili.nomorIzinPondokEmis" name="nomor_izin_pondok_emis" legend="Nomor Izin Operasional / NSPP EMIS Kemenag" required />
                <x-select wire:model="kontakDomisili.jenjangPondok" name="jenjang_pondok" legend="Jenjang Satuan Pendidikan Pondok" :options="['PKPPS', 'PDF', 'SPM']" required />
            </div>
        @elseif($kontakDomisili->statusDomisili === StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
            <div class="mt-4 p-4 border border-slate-200 rounded-sm bg-base-100 space-y-3">
                <x-input wire:model="kontakDomisili.namaPantiAsuhan" name="nama_panti_asuhan" legend="Nama Lembaga / Panti Asuhan" required />
                <x-input wire:model="kontakDomisili.nomorRegistrasiDinsos" name="nomor_registrasi_dinsos" legend="Nomor Izin Registrasi Dinas Sosial" required />
                <x-select wire:model="kontakDomisili.prioritasPanti" name="prioritas_panti" legend="Prioritas Penetapan Dinas Sosial" :options="['Prioritas 1 (P1)', 'Prioritas 2 (P2)']" required />
            </div>
        @endif

        <button wire:click="moveToPinPointStep" class="btn btn-primary w-full mt-4">Lanjut</button>

    @elseif($currentSubStep === 2)
        @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        @endassets

        <h1 class="font-bold text-2xl">Pinpoint Koordinat Rumah</h1>
        <p class="text-gray-800">Lakukan langkah-langkah berikut:</p>

        <ul class="steps steps-vertical">
            <li class="step step-primary">
                <span class="text-left font-medium">Buka website <a class="underline text-blue-500" target="_blank" href="https://google.com/maps">Google Maps</a></span>
            </li>

            <li class="step step-primary mt-3">
                <div class="flex flex-col text-left">
                    <span class="font-medium">Cari lokasi rumah Anda</span>
                    <span class="text-sm text-base-content/70 mt-0.5">
                        Ketik nama jalan, perumahan, atau patokan terdekat di kolom pencarian.
                        Ubah tampilan ke satelit (di pojok kiri bawah) agar atap rumah terlihat jelas.
                        Perbesar (zoom in) peta sampai menemukan atap rumah Anda
                    </span>
                </div>
            </li>

            <li class="step step-primary mt-3">
                <div class="flex flex-col text-left">
                    <span class="font-medium">Salin Koordinat</span>
                    <span class="text-sm text-base-content/70 mt-0.5">
                        Klik kanan tepat di atap rumah Anda, lalu klik baris angka paling atas untuk menyalin
                    </span>
                </div>
            </li>

            <li class="step step-primary">
                <div class="flex flex-col text-left">
                    <span class="font-medium">Tempel Koordinat</span>
                    <span class="text-sm text-base-content/70 mt-0.5">
                        Tempel koordinat yang disalin di bawah ini lalu klik tombol cek
                    </span>
                </div>
            </li>
        </ul>

        <div class="ml-0 sm:ml-2">
            <x-input wire:model="kontakDomisili.pastedCoordinate" name="koordinat" legend="Paste Koordinat" class="max-w-sm" required />
            <button wire:click="renderCoordinate" class="btn btn-primary mt-2">Cek</button>

            {{-- AI GENERATED: Map Container --}}
            <div
                wire:ignore
                x-data="{
                    map: null,
                    marker: null,
                    showMap: false,
                    handleRenderMap(lat, lng) {
                        this.showMap = true;
                        this.$nextTick(() => {
                            this.initMap(lat, lng);
                        });
                    },
                    initMap(lat, lng) {
                        if (!this.map) {
                            this.map = L.map($refs.mapContainer, {
                                dragging: false,
                                zoomControl: false,
                                scrollWheelZoom: false,
                                doubleClickZoom: false,
                                touchZoom: false,
                                keyboard: false
                            }).setView([lat, lng], 17);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(this.map);

                            this.marker = L.marker([lat, lng]).addTo(this.map);
                        } else {
                            this.map.setView([lat, lng], 17);
                            if (this.marker) {
                                this.marker.setLatLng([lat, lng]);
                            } else {
                                this.marker = L.marker([lat, lng]).addTo(this.map);
                            }
                        }

                        setTimeout(() => this.map.invalidateSize(), 150);
                    }
                }"
                x-on:render-map.window="handleRenderMap($event.detail.lat, $event.detail.lng)"
                x-show="showMap"
                style="display: none;"
                class="mt-4 w-full"
            >
                <div x-ref="mapContainer" class="w-full h-72 border border-slate-300 z-0 pointer-events-none"></div>
                <button wire:click="nextStep" class="btn btn-primary mt-4">Konfirmasi</button>
            </div>
        </div>
    @endif
</div>
