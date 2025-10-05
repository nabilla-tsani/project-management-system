<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel users.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // (Opsional) Hapus semua data lama sebelum seed ulang
        DB::table('users')->truncate();

        for ($i = 0; $i < 10; $i++) {
            $name = $faker->unique()->name;
            $username = strtolower(str_replace(' ', '.', $name));

            DB::table('users')->insert([
                'name' => $name,
                'email' => $username . '@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
