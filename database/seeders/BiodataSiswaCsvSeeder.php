<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class BiodataSiswaCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('data/hasil_ekstraksi_murid_lengkap.csv');

        if (file_exists($csvPath)) {
            Artisan::call('app:import-biodata-csv', [
                'file' => $csvPath,
            ]);
        }
    }
}
