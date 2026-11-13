<?php

namespace App\Livewire;

use App\Enums\StatusDomisili;
use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusKhusus;
use App\Enums\StatusPrestasi;
use App\Enums\TahunLulus;
use App\Enums\WilayahSekolahAsal;
use App\Livewire\Forms\AkademikPrestasiSiswaForm;
use App\Livewire\Forms\BiodataSiswaForm;
use App\Livewire\Forms\KontakDomisiliSiswaForm;
use App\Livewire\Forms\UnggahBerkasSiswaForm;
use App\Models\BiodataSiswa;
use App\Models\RegisteredSiswa;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

class RegistrationWizard extends Component
{
    use WithFileUploads;

    protected function createDummyTemporaryFile(string $filename = 'dokumen.pdf'): TemporaryUploadedFile
    {
        // Beri header %PDF-1.4 agar PHP info mendeteksi MIME type PDF yang valid
        $fakeFile = UploadedFile::fake()->createWithContent(
            $filename,
            "%PDF-1.4 \n" . str_repeat('dummy content ', 100)
        );
        $disk = FileUploadConfiguration::disk();

        // Simpan ke direktori temporary Livewire beserta file metadata .json
        $storedPath = FileUploadConfiguration::storeTemporaryFile($fakeFile, $disk);
        return TemporaryUploadedFile::createFromLivewire(basename($storedPath));
    }
    public function mount(): void
    {
        if (app()->isLocal()) {
            // step-1
            $dummy = BiodataSiswa::first();

            $this->biodata->fill($dummy);
            $this->biodata->fill($dummy);
            $this->biodata->tahunLulus = fake()->randomElement(TahunLulus::values());
            $this->biodata->wilayahSekolahAsal = fake()->randomElement(WilayahSekolahAsal::values());
            $this->searchBiodata();
            $this->nextStep();

            // step-2
            $this->kontakDomisili->noWhatsapp = "0123456789";
            $this->kontakDomisili->email = fake()->email();
            $this->kontakDomisili->statusDomisili = StatusDomisili::SesuaiDomisiliPantiAsuhan->value;
            $this->moveToPinPointStep();
            $this->kontakDomisili->pastedCoordinate = '-7.556123, 110.823456';
            $this->renderCoordinate();
            $this->nextStep();

            // step-3
            $this->akademikPrestasi->nilaiRapor = [
                'agama' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
                'ppkn' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
                'indonesia' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
                'matematika' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
                'ipa' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
                'ips' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
                'inggris' => [1 => 50, 2 => 50, 3 => 50, 4 => 50, 5 => 50],
            ];
            $this->submitRaport();
            $this->akademikPrestasi->statusKhusus = StatusKhusus::TidakAda->value;
            $this->akademikPrestasi->statusPrestasi = StatusPrestasi::TidakMemiliki->value;
            $this->akademikPrestasi->statusKepengurusanOrganisasi = StatusKepengurusanOrganisasi::TidakPernah->value;
            $this->nextStep();

            // step-4
            $this->berkas->suratPernyataanKebenaran = $this->createDummyTemporaryFile('fake.pdf');
            $this->berkas->kartuKeluarga = $this->createDummyTemporaryFile('fake.pdf');
            $this->berkas->suratKeteranganNilaiRapor = $this->createDummyTemporaryFile('fake.pdf');
            $this->nextStep();

            // step-5: tetap di step 5 untuk kemudahan pengujian form konfirmasi & submit
        }
    }

    public BiodataSiswaForm $biodata;
    public KontakDomisiliSiswaForm $kontakDomisili;
    public AkademikPrestasiSiswaForm $akademikPrestasi;
    public UnggahBerkasSiswaForm $berkas;

    public int $currentStep = 1;
    public int $currentSubStep = 1;
    public array $availableSteps = [
        1 => ['title' => 'Biodata', 'desc' => ''],
        2 => ['title' => 'Kontak & Domisili', 'desc' => ''],
        3 => ['title' => 'Akademik & Prestasi', 'desc' => ''],
        4 => ['title' => 'Unggah Berkas', 'desc' => ''],
        5 => ['title' => 'Konfirmasi Data', 'desc' => ''],
        6 => ['title' => 'Cetak Bukti', 'desc' => ''],
    ];

    public bool $pernyataanSetuju = false;
    //what is this shit?
    public ?RegisteredSiswa $registeredSiswa = null;

    public function searchBiodata(): void
    {
        if ($this->biodata->searchBiodata()) {
            $this->currentSubStep = 2;
        } else {
            $this->biodata->addError('searchBiodataError', 'Data tidak ditemukan.');
        }
    }

    public function moveToPinPointStep(): void
    {
        $this->kontakDomisili->validateBeforeMoveToPinPointStep();
        $this->currentSubStep = 2;
    }

    public function renderCoordinate(): void
    {
        $this->kontakDomisili->renderCoordinate();

        $this->dispatch(
            'render-map',
            lat: $this->kontakDomisili->latitude,
            lng: $this->kontakDomisili->longitude,
        );
    }

    public function submitRaport(): void
    {
        $this->akademikPrestasi->submitRaport();
        $this->currentSubStep = 2;
    }

    public function submitPrestasi(): void
    {
        $this->akademikPrestasi->submitPrestasi();
        $this->nextStep();
    }

    public function nextStep(): void
    {
        $this->currentSubStep = 1;
        $this->currentStep++;
    }

    public function submitBerkas(): void
    {
        $this->berkas->submitBerkas();
        $this->nextStep();
    }

    public function submitPendaftaran(): void
    {
        $this->validate([
           'pernyataanSetuju' => ['accepted'],
        ], [
            'pernyataanSetuju.accepted' => 'Anda harus mencentang pernyataan bahwa data yang diisi adalah benar.',
        ]);

        // cegah duplikasi pendaftaran
        if (RegisteredSiswa::where('nisn', $this->biodata->nisn)->exists()) {
            $this->addError('pernyataanSetuju', 'Siswa dengan NISN ini sudah pernah didaftarkan.');
            return;
        }

        // Pastikan latitude & longitude terisi jika pastedCoordinate ada
        if (($this->kontakDomisili->latitude === null || $this->kontakDomisili->longitude === null) && filled($this->kontakDomisili->pastedCoordinate)) {
            $this->kontakDomisili->renderCoordinate();
        }

        try {
            $this->registeredSiswa = DB::transaction(function () {
                $totalNilai = 0;
                $count = 0;
                foreach ($this->akademikPrestasi->nilaiRapor as $semesters) {
                    foreach ($semesters as $nilai) {
                        $totalNilai += (float) $nilai;
                        $count++;
                    }
                }

                $rataRata = $count > 0 ? round($totalNilai / $count, 2) : 0;

                $nomorPendaftaran = 'REG-' . date('Y') . '-' . strtoupper(Str::random(6));

                $folderBerkas = 'berkas-pendaftaran/' . $this->biodata->nisn;

                $pathSuratPernyataan = $this->berkas->suratPernyataanKebenaran?->store($folderBerkas, 'public') ?? '';
                $pathKartuKeluarga = $this->berkas->kartuKeluarga?->store($folderBerkas, 'public') ?? '';
                $pathSuratRapor = $this->berkas->suratKeteranganNilaiRapor?->store($folderBerkas, 'public') ?? '';

                $pathPiagam = $this->berkas->piagamPrestasi
                    ? $this->berkas->piagamPrestasi->store($folderBerkas, 'public')
                    : null;

                $pathSkOrganisasi = $this->berkas->skOrganisasi
                    ? $this->berkas->skOrganisasi->store($folderBerkas, 'public')
                    : null;

                $siswa = $this->biodata->biodataDitemukan;

                return RegisteredSiswa::create([
                    'nomor_pendaftaran' => $nomorPendaftaran,
                    'biodata_siswa_id'  => $siswa['id'] ?? null,
                    'nisn'              => $this->biodata->nisn,
                    'nik'               => $this->biodata->nik,
                    'nama_lengkap'      => $siswa['nama_lengkap'] ?? '',
                    'nama_sekolah_asal' => $siswa['nama_sekolah_asal'] ?? '',
                    'tahun_lulus'       => $this->biodata->tahunLulus,
                    'wilayah_sekolah_asal' => $this->biodata->wilayahSekolahAsal,
                    'no_whatsapp'       => $this->kontakDomisili->noWhatsapp,
                    'email'             => $this->kontakDomisili->email,
                    'status_domisili'   => $this->kontakDomisili->statusDomisili,
                    'tgl_cetak_kk'      => $this->kontakDomisili->tglCetakKk ?: null,
                    'pasted_coordinate' => $this->kontakDomisili->pastedCoordinate,
                    'latitude'          => $this->kontakDomisili->latitude,
                    'longitude'         => $this->kontakDomisili->longitude,
                    'nilai_rapor'           => $this->akademikPrestasi->nilaiRapor,
                    'rata_rata_nilai_rapor' => $rataRata,
                    'status_khusus'         => $this->akademikPrestasi->statusKhusus,
                    'status_prestasi'       => $this->akademikPrestasi->statusPrestasi,
                    'kategori_event_prestasi' => $this->akademikPrestasi->kategoriEventPrestasi ?: null,
                    'tingkat_kejuaraan_berjenjang' => $this->akademikPrestasi->tingkatKejuaraanBerjenjang ?: null,
                    'nama_kejuaraan_berjenjang'    => $this->akademikPrestasi->namaKejuaraanBerjenjang ?: null,
                    'peringkat_juara_berjenjang'   => $this->akademikPrestasi->peringkatJuaraBerjenjang ?: null,
                    'tahun_perolehan_kejuaraan_berjenjang' => $this->akademikPrestasi->tahunPerolehanKejuaraanBerjenjang ?: null,
                    'penyelenggara_kejuaraan_berjenjang'   => $this->akademikPrestasi->penyelenggaraKejuaraanBerjenjang ?: null,
                    'status_kepengurusan_organisasi'       => $this->akademikPrestasi->statusKepengurusanOrganisasi,
                    'jenis_organisasi_jabatan'             => $this->akademikPrestasi->jenisOrganisasiJabatan ?: null,
                    'surat_pernyataan_kebenaran_path' => $pathSuratPernyataan,
                    'kartu_keluarga_path'             => $pathKartuKeluarga,
                    'surat_keterangan_nilai_rapor_path' => $pathSuratRapor,
                    'piagam_prestasi_path'            => $pathPiagam,
                    'sk_organisasi_path'              => $pathSkOrganisasi,
                    'is_activated' => false,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            $this->addError('pernyataanSetuju', 'Terjadi kesalahan saat memproses pendaftaran. Silakan coba lagi.');
            return;
        }

        $this->nextStep();
    }

    public function cetakBuktiPendaftaran()
    {
        if (! $this->registeredSiswa) {
            return;
        }

        if (Route::has('pendaftaran.cetak-bukti')) {
            return redirect()->route('pendaftaran.cetak-bukti', [
                'nomor' => $this->registeredSiswa->nomor_pendaftaran,
            ]);
        }

        session()->flash('info', 'Pendaftaran berhasil dengan nomor: ' . $this->registeredSiswa->nomor_pendaftaran);
    }

    public function previousStep(): void
    {
        if ($this->currentStep === 1 && $this->currentSubStep === 1) {
            $this->redirectRoute('index');
            return;
        }
        if ($this->currentStep >= 6) {
            return;
        }

        if ($this->currentSubStep > 1) {
            $this->currentSubStep--;
            return;
        }

        $this->currentStep--;
        $this->currentSubStep = match ($this->currentStep) {
            1, 2, 3 => 2,
            default => 1,
        };
    }

    public function render(): View|Factory
    {
        $wideSteps = ['2.2', '3.1', '4.1', '5.1'];
        $isWideStep = in_array("$this->currentStep.$this->currentSubStep", $wideSteps, true);

        return view('livewire.registration-wizard.index', [
            'maxWidth' => $isWideStep ? 'max-w-4xl' : 'max-w-md'
        ]);
    }
}
