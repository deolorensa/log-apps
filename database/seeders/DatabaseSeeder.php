<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();`

        User::factory()->create([
            'name' => 'direktur',
            'email' => 'direktur@gmail.com',
            'password' => 'test12345',
        ]);
        User::factory()->create([
            'name' => 'manajer1',
            'email' => 'manajer1@gmail.com',
            'direktur' => 1,
            'password' => 'test12345',
        ]);
        User::factory()->create([
            'name' => 'manajer2',
            'email' => 'manajer2@gmail.com',
            'direktur' => 1,
            'password' => 'test12345',
        ]);
        User::factory()->create([
            'name' => 'karyawan1',
            'email' => 'karyawan1@gmail.com',
            'direktur' => 1,
            'manajer' => 2,
            'password' => 'test12345',
        ]);
        User::factory()->create([
            'name' => 'karyawan2',
            'email' => 'karyawan2@gmail.com',
            'direktur' => 1,
            'manajer' => 3,
            'password' => 'test12345',
        ]);
    }
}
