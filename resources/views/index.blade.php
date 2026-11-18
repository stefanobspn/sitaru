<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMB Provinsi Jawa Tengah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 antialiased">

<!-- Header / Navigasi Utama -->
<header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-50 w-full bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <!-- Brand / Logo -->
            <a href="{{ route('index') }}" class="flex items-center gap-3 group">
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg md:text-lg text-gray-900 tracking-tight leading-none group-hover:text-[#165080] transition-colors">
                        SITARU
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-700">
                <a href="#statistik" class="hover:text-[#165080] transition-colors">Statistik</a>
                <a href="#sambutan" class="hover:text-[#165080] transition-colors">Sambutan</a>
                <a href="#faq" class="hover:text-[#165080] transition-colors">FAQ</a>
                <a href="{{ route('seleksi') }}" class="hover:text-[#165080] transition-colors flex items-center gap-1.5">
                    <span>Jurnal Seleksi</span>
                </a>
            </nav>

            <!-- Desktop CTA Buttons -->
            <div class="hidden md:flex items-center gap-2.5">
                <a href="{{ route('siswa') }}" class="text-xs font-semibold px-4 py-2 text-gray-700 hover:text-[#165080] border border-slate-300 rounded-sm hover:border-[#165080] transition">
                    Login Siswa
                </a>
                <a href="{{ route('daftar') }}" class="text-xs font-semibold px-4 py-2 bg-[#165080] text-white rounded-sm hover:bg-[#0E304D] transition">
                    Ajukan Akun
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex md:hidden items-center">
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-sm text-gray-600 hover:text-gray-900 hover:bg-slate-100 focus:outline-none"
                    aria-label="Toggle navigation menu"
                >
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav Drawer -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        @click.outside="mobileMenuOpen = false"
        class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-3"
    >
        <div class="flex flex-col space-y-2 text-sm font-medium text-gray-700">
            <a href="#beranda" @click="mobileMenuOpen = false" class="px-2 py-1.5 rounded-sm hover:bg-slate-100 hover:text-[#165080]">Beranda</a>
            <a href="#alur" @click="mobileMenuOpen = false" class="px-2 py-1.5 rounded-sm hover:bg-slate-100 hover:text-[#165080]">Alur Pendaftaran</a>
            <a href="#statistik" @click="mobileMenuOpen = false" class="px-2 py-1.5 rounded-sm hover:bg-slate-100 hover:text-[#165080]">Statistik</a>
            <a href="{{ route('seleksi') }}" class="px-2 py-1.5 rounded-sm hover:bg-slate-100 hover:text-[#165080] flex items-center justify-between">
                <span>Jurnal Seleksi</span>
                <span class="badge badge-primary badge-xs py-1 px-1.5 text-[9px] text-white">Live</span>
            </a>
            <a href="#sambutan" @click="mobileMenuOpen = false" class="px-2 py-1.5 rounded-sm hover:bg-slate-100 hover:text-[#165080]">Sambutan</a>
            <a href="#faq" @click="mobileMenuOpen = false" class="px-2 py-1.5 rounded-sm hover:bg-slate-100 hover:text-[#165080]">FAQ</a>
        </div>
        <div class="pt-3 border-t border-slate-200 flex flex-col gap-2">
            <a href="{{ route('siswa') }}" class="text-center text-xs font-semibold px-4 py-2.5 text-gray-700 border border-slate-300 rounded-sm hover:bg-slate-50 transition">
                Login Siswa
            </a>
            <a href="{{ route('daftar') }}" class="text-center text-xs font-semibold px-4 py-2.5 bg-[#165080] text-white rounded-sm hover:bg-[#0E304D] transition">
                Ajukan Akun Baru
            </a>
            <div class="grid grid-cols-2 gap-2 pt-1 text-[11px] text-center text-gray-500">
                <a href="{{ route('operator') }}" class="hover:text-blue-600 underline">Portal Operator</a>
                <a href="{{ route('admin') }}" class="hover:text-blue-600 underline">Portal Admin</a>
            </div>
        </div>
    </div>
</header>

<main class="w-full">
    <!-- Hero Section -->
    <section id="beranda" class="bg-[#165080] w-full p-6 md:p-16">
        <p class="text-white text-sm md:text-base opacity-90">Selamat datang di</p>
        <h1 class="text-white text-3xl md:text-4xl font-bold max-w-xl leading-tight mt-2">
            Sistem Penerimaan Murid Baru (SPMB) Provinsi Jawa Tengah Jenjang SMA dan SMK
        </h1>

        <div class="flex flex-wrap gap-3 mt-6 md:mt-8">
            <a href="{{ route('daftar') }}" class="inline-block bg-white text-[#165080] font-semibold px-8 py-2.5 rounded-sm hover:bg-gray-100 transition">
                Ajukan Akun
            </a>
            <a href="{{ route('aktivasi') }}" class="inline-block bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-sm hover:bg-blue-800 transition">
                Aktivasi Akun
            </a>
            <a href="{{ route('siswa') }}" class="inline-block bg-transparent text-white border border-white font-semibold px-6 py-2.5 rounded-sm hover:bg-white/10 transition">
                Login Siswa
            </a>
        </div>

        <!-- 6 Kartu (Responsif: 2 kolom di HP, 3 di tablet, 6 di desktop) -->
        <div id="alur" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mt-16 md:mt-24">
            <a href="{{ route('daftar') }}" class="block w-full">
                <x-card-agenda agenda="pendaftaran"/>
            </a>
            <div class="w-full">
                <x-card-agenda agenda="verifikasi"/>
            </div>
            <a href="{{ route('aktivasi') }}" class="block w-full">
                <x-card-agenda agenda="aktivasi"/>
            </a>
            <a href="{{ route('siswa') }}" class="block w-full">
                <x-card-agenda agenda="daftar_sekolah"/>
            </a>
            <a href="{{ route('seleksi') }}" class="block w-full">
                <x-card-agenda agenda="hasil_seleksi"/>
            </a>
            <div class="w-full">
                <x-card-agenda agenda="daftar_ulang"/>
            </div>
        </div>
    </section>

    <!-- Live Statistik -->
    <section id="statistik" class="bg-[#0E304D] w-full p-8 md:p-16 scroll-mt-64">
        <h2 class="text-white text-2xl md:text-3xl font-bold text-center">Live Statistik</h2>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mt-8">
            {{-- Total Pendaftar --}}
            <div class="bg-[#165080] h-32 w-full sm:w-64 rounded-md flex flex-col items-center justify-center p-4">
                <span class="text-white text-5xl font-bold">{{ number_format($totalPendaftar) }}</span>
                <span class="mt-3 text-white font-medium">Total Pendaftar</span>
            </div>
            {{-- Pendaftar Hari Ini  --}}
            <div class="bg-[#165080] h-32 w-full sm:w-64 rounded-md flex flex-col items-center justify-center p-4">
                <span class="text-white text-5xl font-bold">{{ number_format($pendaftarHariIni) }}</span>
                <span class="mt-3 text-white font-medium">Pendaftar Hari Ini</span>
            </div>
            {{-- Total Kuota --}}
            <div class="bg-[#165080] h-32 w-full sm:w-64 rounded-md flex flex-col items-center justify-center p-4">
                <span class="text-white text-5xl font-bold">{{ number_format($totalKuota) }}</span>
                <span class="mt-3 text-white font-medium">Total Kuota Tersedia</span>
            </div>
        </div>
    </section>

    <!-- Kata Sambutan -->
    <section id="sambutan" class="bg-[#D0DCE6] w-full p-8 md:p-16 scroll-mt-64">
        <h2 class="text-[#333B40] text-2xl md:text-3xl font-bold text-center mb-10">Kata Sambutan</h2>

        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-10 items-center md:items-start">
            <!-- Foto & Nama -->
            <div class="w-full md:w-56 shrink-0 flex flex-col items-center">
                <img
                    src="{{ asset('images/gubernur-jateng.png') }}"
                    alt="Ahmad Luthfi - Gubernur Jawa Tengah"
                    class="w-56 h-72 object-cover object-top rounded-sm border border-slate-300 bg-white"
                />
                <p class="mt-4 font-semibold text-gray-800 text-sm text-center">Gubernur Jawa Tengah</p>
                <p class="text-gray-700 font-medium text-sm text-center">Ahmad Luthfi</p>
            </div>

            <!-- Teks Sambutan -->
            <div class="flex-1 text-gray-800 text-sm md:text-[15px] leading-relaxed space-y-4">
                <p class="font-bold">Assalaamu’alaikum Wr. Wb</p>

                <p class="text-justify">
                    Puji syukur kita panjatkan ke hadirat Tuhan Yang Maha Esa, karena atas rahmat dan karunia-Nya,
                    Pemerintah Provinsi Jawa Tengah dapat melaksanakan penerimaan murid baru melalui aplikasi Sistem
                    Penerimaan Murid Baru (SPMB) untuk Sekolah Menengah Atas, Sekolah Menengah Kejuruan, dan Sekolah
                    Luar Biasa di Provinsi Jawa Tengah.
                </p>
                <p class="text-justify">
                    Pendidikan adalah fondasi utama dalam membangun masa depan bangsa. Melalui SPMB berbasis digital
                    ini, Provinsi Jawa Tengah berkomitmen menghadirkan proses yang lebih transparan, akuntabel, dan
                    mudah diakses oleh seluruh masyarakat. Dengan demikian, setiap anak di Jawa Tengah memiliki
                    kesempatan yang sama untuk memperoleh pendidikan yang berkualitas sesuai minat dan bakatnya.
                </p>
                <p class="text-justify">
                    Dalam penyelenggaraan SPMB ini, Pemerintah Provinsi Jawa Tengah menegaskan prinsip-prinsip dasar
                    yang harus dijunjung tinggi, yaitu berintegritas, objektif, transparan, akuntabel, tidak
                    diskriminatif, dan berkeadilan. NO TITIP-NO JASTIP. Prinsip-prinsip ini menjadi pedoman agar
                    setiap tahapan penerimaan murid baru berjalan dengan jujur, adil, dan dapat
                    dipertanggung-jawabkan, sehingga kepercayaan masyarakat terhadap sistem pendidikan semakin kuat.
                </p>
                <p class="text-justify">
                    Mari kita sambut SPMB dengan komitmen yang sama, yaitu menghadirkan pendidikan untuk seluruh
                    lapisan masyarakat, serta menumbuhkan kepedulian kepada anak-anak yang memerlukan perhatian
                    khusus.
                </p>
                <p class="text-justify">
                    Saya yakin masyarakat Jawa Tengah mampu menjadi teladan praktik baik, menunjukkan bahwa
                    kompetisi bukan sekadar perebutan kursi, melainkan ruang untuk menguji sportivitas, motivasi,
                    dan inspirasi.
                </p>
                <p class="text-justify">
                    Selamat mengikuti tahapan dan proses penyelenggaraan SPMB. Kiranya dari proses SPMB ini, kita
                    akan melihat anak-anak kita bertumbuh untuk terwujudnya Indonesia Emas 2045.
                </p>
                <div class="pt-2">
                    <p>Terima kasih</p>
                    <p class="font-bold">Wassalamu’alaikum Wr. Wb</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="bg-white w-full p-8 md:p-16 scroll-mt-24">
        <h2 class="text-[#333B40] text-2xl md:text-3xl font-bold text-center mt-12 md:mt-16 mb-10">Pertanyaan yang Sering Diajukan (FAQ)</h2>

        <div class="w-full flex justify-center items-center flex-col gap-4 max-w-3xl mx-auto">
            <x-accordion
                title="Apa itu SITARU SPMB Provinsi Jawa Tengah?"
                content="SITARU (Sistem Informasi Terpadu Peserta Didik Baru) adalah sistem resmi penerimaan murid baru jenjang SMA dan SMK Negeri di lingkungan Pemerintah Provinsi Jawa Tengah yang diselenggarakan secara daring, berintegritas, transparan, objektif, dan akuntabel tanpa dipungut biaya apapun."
            />
            <x-accordion
                title="Bagaimana tahapan dan alur pendaftaran calon siswa baru?"
                content="Alur pendaftaran terdiri dari 5 tahapan utama: (1) Pengajuan Akun mandiri secara online melalui menu 'Ajukan Akun' dan mencetak Bukti Pendaftaran; (2) Verifikasi Berkas fisik di SMA/SMK Negeri terdekat untuk mendapatkan Kode Aktivasi dari operator sekolah; (3) Aktivasi Akun dan pembuatan kata sandi di menu 'Aktivasi Akun'; (4) Login Siswa untuk memilih sekolah serta jurusan peminatan; dan (5) Memantau Jurnal Seleksi hingga pelaksanaan Daftar Ulang sesuai jadwal resmi."
            />
            <x-accordion
                title="Bagaimana mekanisme dan formula perhitungan Nilai Akhir (NA) seleksi?"
                content="Perhitungan Nilai Akhir (NA) dilakukan secara otomatis dan merit-based menggunakan rumus resmi: NA = (50% x Nilai Rata-rata Rapor Semester 1-5) + (50% x Nilai Tes Kemampuan Akademik / NRTKA) + Nilai Kejuaraan (NK) + Nilai Organisasi (NO). Khusus calon siswa dengan kejuaraan internasional atau juara 1 nasional berjenjang diprioritaskan langsung diterima."
            />
            <x-accordion
                title="Berapa banyak pilihan sekolah atau jurusan yang dapat dipilih?"
                content="Calon siswa dapat menentukan hingga 2 pilihan. Untuk jenjang SMA, siswa dapat memilih 2 sekolah SMA tujuan. Untuk jenjang SMK, siswa dapat memilih hingga 2 program keahlian / jurusan pada sekolah yang sama maupun berbeda. Ketentuan penting: Pilihan 1 dan Pilihan 2 wajib berada pada jenis sekolah yang sama (sesama SMA atau sesama SMK)."
            />
            <x-accordion
                title="Bagaimana jika calon siswa lupa kata sandi (password) akun?"
                content="Demi menjaga keamanan dan integritas akun pendaftar, mekanisme reset kata sandi hanya dapat dilakukan oleh Operator Sekolah yang memverifikasi berkas pendaftaran Anda. Calon siswa atau orang tua/wali dapat menghubungi atau mendatangi operator sekolah verifikator dengan membawa identitas diri dan lembar Bukti Pendaftaran untuk permohonan reset kata sandi."
            />
        </div>
    </section>
</main>

<!-- Semantic Footer -->
<footer class="bg-[#0B1E30] text-slate-300 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="flex flex-col md:flex-row justify-between items-start gap-8 md:gap-12 lg:gap-16">
            <!-- Kolom 1: Profil SITARU -->
            <div class="space-y-4 max-w-md">
                <span class="block text-white font-extrabold text-xl tracking-tight leading-none">
                    SITARU
                </span>

                <p class="text-xs text-slate-300 leading-relaxed">
                    Sistem Informasi Terpadu Peserta Didik Baru
                </p>
            </div>

            <!-- Kolom 2: Kontak & Informasi -->
            <div class="space-y-3 text-xs max-w-md">
                <h3 class="text-white font-bold text-sm tracking-wider uppercase mb-4">Layanan Pengaduan & Informasi</h3>
                <p class="font-semibold text-slate-200">
                    Dinas Pendidikan dan Kebudayaan Provinsi Jawa Tengah
                </p>
                <p class="text-slate-400 leading-relaxed">
                    Jl. Pemuda No. 134, Sekayu, Kec. Semarang Tengah, Kota Semarang, Jawa Tengah 50132
                </p>
                <div class="pt-1 space-y-1.5 text-slate-300">
                    <p class="flex items-center gap-2">
                        <span class="font-medium text-slate-400">Waktu Layanan:</span>
                        Senin - Jumat (08.00 - 15.30 WIB)
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="font-medium text-slate-400">Email:</span>
                        <a href="mailto:spmb@pdkjateng.go.id" class="text-blue-400 hover:underline">spmb@pdkjateng.go.id</a>
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="font-medium text-slate-400">Helpdesk:</span>
                        <span>(024) 86041888 / Call Center 1500-115</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright Bar -->
    <div class="border-t border-slate-800 bg-[#081726]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
            <p>
                &copy; {{ date('Y') }} Dinas Pendidikan dan Kebudayaan Provinsi Jawa Tengah. Hak cipta dilindungi.
            </p>
        </div>
    </div>
</footer>

</body>

</html>
