<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMB Provinsi Jawa Tengah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 antialiased">

<!-- Header -->
<header class="w-full bg-white h-16 md:h-18 border-b border-gray-100 flex items-center justify-center">
    <h1 class="text-base md:text-lg font-bold">Header Full Width</h1>
</header>

<main class="w-full">
    <!-- Hero Section -->
    <section class="bg-[#165080] w-full p-6 md:p-16">
        <p class="text-white text-sm md:text-base opacity-90">Selamat datang di</p>
        <h1 class="text-white text-3xl md:text-4xl font-bold max-w-xl leading-tight mt-2">
            Sistem Penerimaan Murid Baru (SPMB) Provinsi Jawa Tengah Jenjang SMA dan SMK
        </h1>

        <a href="/daftar" class="inline-block bg-white text-[#165080] font-semibold px-8 py-2.5 rounded-sm shadow-sm hover:bg-gray-100 transition mt-6 md:mt-8">
            Ajukan Akun
        </a>

        <!-- 6 Kartu (Responsif: 2 kolom di HP, 3 di tablet, 6 di desktop) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mt-16 md:mt-24">
            <x-card-agenda agenda="pendaftaran" currentAgenda/>
            <x-card-agenda agenda="verifikasi"/>
            <x-card-agenda agenda="aktivasi"/>
            <x-card-agenda agenda="daftar_sekolah"/>
            <x-card-agenda agenda="hasil_seleksi"/>
            <x-card-agenda agenda="daftar_ulang"/>
        </div>
    </section>

    <!-- Live Statistik -->
    <section class="bg-[#0E304D] w-full p-8 md:p-16">
        <h2 class="text-white text-2xl md:text-3xl font-bold text-center">Live Statistik</h2>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mt-8">
            {{-- Total Pendaftar --}}
            <div class="bg-[#165080] h-32 w-full sm:w-64 rounded-md flex flex-col items-center justify-center p-4">
                <span class="text-white text-5xl font-bold">56</span>
                <span class="mt-3 text-white font-medium">Total Pendaftar</span>
            </div>
            {{-- Pendaftar Hari Ini  --}}
            <div class="bg-[#165080] h-32 w-full sm:w-64 rounded-md flex flex-col items-center justify-center p-4">
                <span class="text-white text-5xl font-bold">70</span>
                <span class="mt-3 text-white font-medium">Pendaftar Hari Ini</span>
            </div>
            {{-- Total Kuota --}}
            <div class="bg-[#165080] h-32 w-full sm:w-64 rounded-md flex flex-col items-center justify-center p-4">
                <span class="text-white text-5xl font-bold">80</span>
                <span class="mt-3 text-white font-medium">Total Kuota Tersedia</span>
            </div>
        </div>
    </section>

    <!-- Kata Sambutan -->
    <section class="bg-[#D0DCE6] w-full p-8 md:p-16">
        <h2 class="text-[#333B40] text-2xl md:text-3xl font-bold text-center mb-10">Kata Sambutan</h2>

        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-10 items-center md:items-start">
            <!-- Foto Placeholder & Nama -->
            <div class="w-full md:w-56 shrink-0 flex flex-col items-center">
                <div class="w-56 h-72 bg-gray-400 rounded-sm shadow-md"></div>
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

    <!-- Alat & FAQ -->
    <section class="bg-white w-full p-8 md:p-16">
        <h2 class="text-[#333B40] text-2xl md:text-3xl font-bold text-center">Alat</h2>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mt-8">
            <div class="bg-[#D4E3F2] h-32 w-full sm:w-64 rounded-sm"></div>
            <div class="bg-[#D4E3F2] h-32 w-full sm:w-64 rounded-sm"></div>
            <div class="bg-[#D4E3F2] h-32 w-full sm:w-64 rounded-sm"></div>
        </div>

        <h2 class="text-[#333B40] text-2xl md:text-3xl font-bold text-center mt-20 md:mt-28 mb-8">FAQ</h2>

        <div class="w-full flex justify-center items-center">
            <x-accordion title="Test Title" content="Test Content" />
        </div>

    </section>
</main>

<!-- Semantic Footer -->
<footer class="bg-white w-full py-16 flex justify-center items-center border-t border-gray-100">
    <span class="font-bold text-2xl text-gray-800">FOOTER</span>
</footer>

</body>

</html>
