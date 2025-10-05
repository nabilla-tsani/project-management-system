<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            DB::table('customer')->insert([
                'nama'          => $faker->name,
                'alamat'        => $faker->address,
                'nomor_telepon' => $faker->phoneNumber,
                'email'         => $faker->unique()->safeEmail,
                'catatan'       => $faker->sentence,
                'status'        => 'aktif',
            ]);
        }
    }
}
