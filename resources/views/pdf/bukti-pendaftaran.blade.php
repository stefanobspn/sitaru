<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran - {{ $siswa->nomor_pendaftaran }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #111;
            line-height: 1.5;
        }

        /* Utility Page Break */
        .page-break {
            page-break-after: always;
        }

        /* ================= STYLE HALAMAN 1 ================= */
        .cover-header {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 50px;
        }
        .cover-title {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .cover-subtitle {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 6px;
        }
        .section-petunjuk-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }
        ol.petunjuk-list {
            margin: 0;
            padding-left: 20px;
        }
        ol.petunjuk-list > li {
            margin-bottom: 8px;
            text-align: justify;
        }
        ol.sub-list {
            margin-top: 4px;
            padding-left: 20px;
            list-style-type: lower-alpha;
        }
        ol.sub-list > li {
            margin-bottom: 3px;
        }

        /* ================= STYLE HALAMAN 2 ================= */
        .staples-box {
            border: 1px dashed #555;
            padding: 4px 10px;
            font-size: 8pt;
            letter-spacing: 2px;
            color: #555;
            display: inline-block;
            margin-bottom: 15px;
        }
        .halaman-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: top;
        }
        .col-label { width: 22%; font-weight: 500; }
        .col-sep { width: 2%; text-align: center; }
        .col-val { width: 26%; }

        /* Footer nomor halaman */
        .footer-page-number {
            position: absolute;
            bottom: 0;
            right: 0;
            font-size: 10pt;
            color: #555;
        }
    </style>
</head>
<body>

{{-- ======================================================== --}}
{{--                     HALAMAN 1: PETUNJUK                  --}}
{{-- ======================================================== --}}
<div class="halaman-1">
    <div class="staples-box">STAPLES</div>

    <div class="cover-header">
        <h1 class="cover-title">BUKTI PENDAFTARAN AKUN SITARU</h1>
        <div class="cover-subtitle">NAMA: {{ strtoupper($siswa->nama_lengkap) }}</div>
    </div>

    <div class="section-petunjuk-title">PETUNJUK</div>
    <ol class="petunjuk-list">
        <li>Print dokumen ini dengan format A4 hitam-putih, berikan staples pada bagian yang ditandai.</li>
        <li>Silahkan datang ke SMA/SMK terdekat untuk melakukan aktivasi akun dengan membawa dokumen yang diperlukan.</li>
        <li>
            Dokumen yang dibawa:
            <ol class="sub-list">
                <li>Bukti Pendaftaran Akun SITARU</li>
                <li>Surat Pernyataan Kebenaran Dokumen</li>
                <li>FC Kartu Keluarga</li>
                <li>FC Surat Keterangan Nilai Rapor</li>

                {{-- Conditional Documents sesuai data berkas siswa --}}
                @if ($siswa->piagam_prestasi_path || $siswa->status_prestasi === 'memiliki')
                    <li>FC Piagam / Sertifikat Prestasi Kejuaraan</li>
                @endif

                @if ($siswa->sk_organisasi_path || $siswa->status_kepengurusan_organisasi === 'pernah_mengurus')
                    <li>FC Surat Keputusan (SK) Kepengurusan Organisasi</li>
                @endif
            </ol>
        </li>
    </ol>

    <div class="footer-page-number">1</div>
</div>

{{-- ================= PEMISAH HALAMAN ================= --}}
<div class="page-break"></div>

{{-- ======================================================== --}}
{{--                     HALAMAN 2: DATA & TABEL              --}}
{{-- ======================================================== --}}
<div class="halaman-2">
    <div class="staples-box">STAPLES</div>

    <div class="halaman-title">Biodata</div>
    <table class="data-table">
        <tr>
            <td class="col-label">NISN</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nisn }}</td>
            <td class="col-label">Wilayah Sekolah Asal</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->wilayah_sekolah_asal }}</td>
        </tr>
        <tr>
            <td class="col-label">NIK</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nik }}</td>
            <td class="col-label">Provinsi</td>
            <td class="col-sep">:</td>
            <td class="col-val">Jawa Tengah</td>
        </tr>
        <tr>
            <td class="col-label">Nama Lengkap</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nama_lengkap }}</td>
            <td class="col-label">Kab/Kota</td>
            <td class="col-sep">:</td>
            <td class="col-val">Kota Surakarta</td>
        </tr>
        <tr>
            <td class="col-label">Nama Sekolah Asal</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nama_sekolah_asal }}</td>
            <td class="col-label">Kecamatan</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Tempat Lahir</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa->tempat_lahir ?? '-' }}</td>
            <td class="col-label">Kelurahan</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa->kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Tanggal Lahir</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa?->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
            <td class="col-label">RT</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa->rt ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Alamat</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa->alamat ?? '-' }}</td>
            <td class="col-label">RW</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->biodataSiswa->rw ?? '-' }}</td>
        </tr>
    </table>

    <div class="halaman-title">Kontak & Domisili</div>
    <table class="data-table">
        <tr>
            <td class="col-label">Nomor WhatsApp</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->no_whatsapp }}</td>
            <td class="col-label">Latitude</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->latitude }}</td>
        </tr>
        <tr>
            <td class="col-label">Email</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->email }}</td>
            <td class="col-label">Longitude</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->longitude }}</td>
        </tr>
        <tr>
            <td class="col-label">Status Domisili</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->status_domisili }}</td>
            @if ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiKK->value)
                <td class="col-label">Tanggal Cetak KK</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->tgl_cetak_kk?->format('d/m/Y') ?? ($siswa->tgl_cetak_kk ?? '-') }}</td>
            @elseif ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiMutasiOrtuWali->value)
                <td class="col-label">Instansi Penugasan</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->nama_instansi_mutasi ?? '-' }}</td>
            @elseif ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiDomisiliPondok->value)
                <td class="col-label">Pondok Pesantren</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->nama_pondok_pesantren ?? '-' }}</td>
            @elseif ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
                <td class="col-label">Panti Asuhan</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->nama_panti_asuhan ?? '-' }}</td>
            @else
                <td class="col-label">-</td>
                <td class="col-sep">:</td>
                <td class="col-val">-</td>
            @endif
        </tr>
        @if ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiMutasiOrtuWali->value)
            <tr>
                <td class="col-label">No. SK Mutasi</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->nomor_surat_penugasan ?? '-' }}</td>
                <td class="col-label">Anak Guru / Tendik</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->is_anak_guru ? 'Ya' : 'Tidak' }}</td>
            </tr>
        @elseif ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiDomisiliPondok->value)
            <tr>
                <td class="col-label">No. EMIS/NSPP</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->nomor_izin_pondok_emis ?? '-' }}</td>
                <td class="col-label">Jenjang Pondok</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->jenjang_pondok ?? '-' }}</td>
            </tr>
        @elseif ($siswa->status_domisili === \App\Enums\StatusDomisili::SesuaiDomisiliPantiAsuhan->value)
            <tr>
                <td class="col-label">No. Reg. Dinsos</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->nomor_registrasi_dinsos ?? '-' }}</td>
                <td class="col-label">Prioritas Panti</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $siswa->prioritas_panti ?? '-' }}</td>
            </tr>
        @endif
    </table>

    <div class="halaman-title">Akademik & Nilai Akhir</div>
    <table class="data-table">
        <tr>
            <td class="col-label">Rata-rata Rapor (NRR)</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ number_format($siswa->rata_rata_nilai_rapor, 2) }}</td>
            <td class="col-label">Nilai TKA (NRTKA)</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nrtka !== null ? number_format($siswa->nrtka, 2) : '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Nilai Kejuaraan (NK)</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nilai_kejuaraan ?? '0' }}</td>
            <td class="col-label">Nilai Organisasi (NO)</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->nilai_organisasi ?? '0' }}</td>
        </tr>
        <tr>
            <td class="col-label">Nilai Akhir (NA)</td>
            <td class="col-sep">:</td>
            <td class="col-val" style="font-weight: bold;">{{ $siswa->nilai_akhir !== null ? number_format($siswa->nilai_akhir, 2) : '-' }}</td>
            <td class="col-label">Status Prestasi</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $siswa->status_prestasi ?? '-' }}</td>
        </tr>
    </table>

    @if (!empty($activationCode) || !empty($siswa->activation_code))
        <div class="halaman-title">Aktivasi Akun</div>
        <table class="data-table">
            <tr>
                <td class="col-label">Activation Code</td>
                <td class="col-sep">:</td>
                <td class="col-val" style="font-weight: bold; letter-spacing: 1px;">{{ $activationCode ?? $siswa->activation_code }}</td>
                <td class="col-label">NIP Operator</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $operatorNip ?? $siswa->operator_nip ?? '-' }}</td>
            </tr>
        </table>
    @endif

    <div class="footer-page-number">2</div>
</div>

</body>
</html>
