<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sekolah>
 */
class SekolahFactory extends Factory
{
    protected $model = Sekolah::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'npsn' => $this->faker->unique()->numerify('203#####'),
            'nama_sekolah' => 'SMA Negeri '.fake()->numberBetween(1, 15).' '.fake()->city(),
            'jenis_sekolah' => 'SMA',
            'operator_id' => null,
            'kuota' => 50,
        ];
    }

    public function smk(array $jurusans = ['Rekayasa Perangkat Lunak', 'Teknik Komputer dan Jaringan']): static
    {
        return $this->state(fn () => [
            'jenis_sekolah' => 'SMK',
            'nama_sekolah' => 'SMK Negeri '.fake()->numberBetween(1, 10).' '.fake()->city(),
        ])->afterCreating(function (Sekolah $sekolah) use ($jurusans) {
            foreach ($jurusans as $namaJurusan) {
                $sekolah->jurusans()->create(['nama_jurusan' => $namaJurusan]);
            }
        });
    }

    public function withOperator(?User $operator = null): static
    {
        return $this->state(fn () => [
            'operator_id' => $operator?->id ?? User::factory()->create([
                'name' => null,
                'role' => UserRole::Operator,
                'nip' => $this->faker->unique()->numerify('19850101########'),
            ])->id,
        ]);
    }
}
