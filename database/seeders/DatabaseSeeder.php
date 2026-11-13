<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BiodataSiswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@sitaru.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        User::query()->updateOrCreate(
            ['nip' => '198501012010011001'],
            [
                'name' => 'Operator Sekolah',
                'email' => 'operator@sitaru.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Operator,
            ]
        );

        BiodataSiswa::factory()->count(10)->create();
    }
}
