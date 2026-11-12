<?php

namespace Database\Factories;

use App\Models\BiodataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class BiodataSiswaFactory extends Factory
{
    protected $model = BiodataSiswa::class;
    public function definition(): array
    {
        return [
            'nisn' => $this->faker->unique()->numerify('00########'),
            'nik' => $this->faker->unique()->numerify('3307############'),
            'nama_lengkap' => $this->faker->name(),
            'nama_sekolah_asal' => 'SMP Negeri ' . fake()->numberBetween(1, 10) . ' ' . fake()->city(),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '-15 years'),
            'alamat' => $this->faker->streetAddress(),
            'provinsi' => 'Jawa Tengah',
            'kabupaten_kota' => 'Surakarta',
            'kecamatan' => 'Banjarsari',
            'kelurahan' => 'Ngawi',
            'rt' => $this->faker->numerify('0##'),
            'rw' => $this->faker->numerify('0##'),
        ];
    }
}
