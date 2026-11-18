<?php

namespace App\Console\Commands;

use App\Models\BiodataSiswa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportBiodataCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-biodata-csv 
                            {file? : Path file CSV yang akan diimpor (default: database/data/hasil_ekstraksi_murid_lengkap.csv)}
                            {--truncate : Hapus data lama sebelum mengimpor data baru}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengimpor data biodata calon siswa dari file CSV ke tabel biodata_siswas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file') ?: database_path('data/hasil_ekstraksi_murid_lengkap.csv');

        // Dukung pemuaian simbol tilde (~) di Linux / Unix
        if (str_starts_with($filePath, '~/')) {
            $home = getenv('HOME') ?: ($_SERVER['HOME'] ?? '');
            $filePath = $home.substr($filePath, 1);
        }

        if (! file_exists($filePath)) {
            $this->error("File CSV tidak ditemukan pada path: {$filePath}");

            return self::FAILURE;
        }

        $this->info("Membaca file CSV dari: {$filePath}");

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->error("Gagal membuka file CSV: {$filePath}");

            return self::FAILURE;
        }

        $header = fgetcsv($handle);
        if (! $header) {
            $this->error('File CSV kosong atau format header tidak valid.');
            fclose($handle);

            return self::FAILURE;
        }

        // Hapus BOM UTF-8 jika ada pada kolom pertama
        $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string) $header[0]);
        $header = array_map('trim', $header);

        $expectedColumns = [
            'nisn', 'nik', 'nama_lengkap', 'nama_sekolah_asal', 'tempat_lahir',
            'tanggal_lahir', 'alamat', 'provinsi', 'kabupaten_kota', 'kecamatan',
            'kelurahan', 'rt', 'rw',
        ];

        $missing = array_diff($expectedColumns, $header);
        if (! empty($missing)) {
            $this->error('Kolom CSV tidak sesuai. Kolom berikut tidak ditemukan: '.implode(', '.array_values($missing)));
            fclose($handle);

            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            if ($this->input->isInteractive()) {
                if ($this->confirm('Apakah Anda yakin ingin mengosongkan tabel biodata_siswas terlebih dahulu?', true)) {
                    BiodataSiswa::truncate();
                    $this->warn('Tabel biodata_siswas telah dikosongkan.');
                }
            } else {
                BiodataSiswa::truncate();
                $this->warn('Tabel biodata_siswas telah dikosongkan.');
            }
        }

        $existingNisns = BiodataSiswa::pluck('id', 'nisn')->all();

        $toInsert = [];
        $insertedCount = 0;
        $updatedCount = 0;
        $batchSize = 250;

        $this->output->progressStart();

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < count($header)) {
                    continue;
                }

                $row = array_combine($header, $data);
                $nisn = trim($row['nisn'] ?? '');

                if ($nisn === '') {
                    continue;
                }

                $cleaned = [
                    'nisn' => $nisn,
                    'nik' => trim($row['nik'] ?? ''),
                    'nama_lengkap' => trim($row['nama_lengkap'] ?? ''),
                    'nama_sekolah_asal' => trim($row['nama_sekolah_asal'] ?? '') ?: '-',
                    'tempat_lahir' => trim($row['tempat_lahir'] ?? '') ?: '-',
                    'tanggal_lahir' => trim($row['tanggal_lahir'] ?? ''),
                    'alamat' => trim($row['alamat'] ?? '') ?: '-',
                    'provinsi' => trim($row['provinsi'] ?? '') ?: 'Jawa Tengah',
                    'kabupaten_kota' => trim($row['kabupaten_kota'] ?? '') ?: '-',
                    'kecamatan' => trim($row['kecamatan'] ?? '') ?: '-',
                    'kelurahan' => trim($row['kelurahan'] ?? '') ?: '-',
                    'rt' => trim($row['rt'] ?? '') ?: '-',
                    'rw' => trim($row['rw'] ?? '') ?: '-',
                    'nrtka' => isset($row['nrtka']) && is_numeric($row['nrtka']) ? (float) $row['nrtka'] : round(mt_rand(5000, 9000) / 100, 2),
                ];

                if (isset($existingNisns[$nisn])) {
                    BiodataSiswa::where('id', $existingNisns[$nisn])->update($cleaned);
                    $updatedCount++;
                } else {
                    $toInsert[] = $cleaned;
                    $insertedCount++;

                    if (count($toInsert) >= $batchSize) {
                        BiodataSiswa::insert($toInsert);
                        $toInsert = [];
                    }
                }

                $this->output->progressAdvance();
            }

            if (! empty($toInsert)) {
                BiodataSiswa::insert($toInsert);
            }

            DB::commit();
            $this->output->progressFinish();
            fclose($handle);

            $this->newLine();
            $this->table(
                ['Keterangan', 'Jumlah Data'],
                [
                    ['Data Baru Diimpor', $insertedCount],
                    ['Data Diperbarui', $updatedCount],
                    ['Total Biodata di Database', BiodataSiswa::count()],
                ]
            );

            $this->info('Proses impor data biodata siswa selesai dengan sukses!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            $this->newLine();
            $this->error('Terjadi kesalahan saat mengimpor data: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
