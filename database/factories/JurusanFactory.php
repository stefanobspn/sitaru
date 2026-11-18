<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jurusan>
 */
class JurusanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sekolah_id' => Sekolah::factory(),
            'nama_jurusan' => fake()->randomElement([
                'Rekayasa Perangkat Lunak',
                'Teknik Komputer dan Jaringan',
                'Desain Komunikasi Visual',
                'Akuntansi dan Keuangan Lembaga',
                'Manajemen Perkantoran',
                'Teknik Kendaraan Ringan',
                'Teknik Pengelasan',
            ]),
        ];
    }
}
