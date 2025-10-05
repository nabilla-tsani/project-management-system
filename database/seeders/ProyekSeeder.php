<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyek;
use App\Models\Customer;
use App\Models\User;

class ProyekSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua data customer & user
        $customers = Customer::all();
        $users = User::all();

        if ($customers->count() === 0 || $users->count() === 0) {
            $this->command->warn('Seeder gagal: Customer atau User belum ada data!');
            return;
        }

        // List status proyek
        $statusList = ['belum_dimulai', 'sedang_berjalan', 'selesai'];

        // Buat 10 proyek
        for ($i = 1; $i <= 20; $i++) {
            $customer = $customers->random(); // customer acak
            $status   = $statusList[array_rand($statusList)]; // status acak

            $proyek = Proyek::create([
                'nama_proyek'     => "Proyek Ke-$i",
                'customer_id'     => $customer->id,
                'deskripsi'       => "Deskripsi proyek ke-$i",
                'lokasi'          => "Lokasi ke-$i",
                'tanggal_mulai'   => now(),
                'tanggal_selesai' => now()->addMonths(rand(3, 12)),
                'anggaran'        => rand(500000000, 2000000000),
                'status'          => $status,
            ]);

            // Ambil user acak (2 sampai 5 user untuk setiap proyek)
            $randomUsers = $users->random(rand(2, 5));

            foreach ($randomUsers as $user) {
                $proyek->users()->attach($user->id, [
                    'sebagai'    => fake()->randomElement(['manajer proyek', 'programmer', 'tester']),
                    'keterangan' => 'Ditugaskan pada proyek ke-' . $i,
                ]);
            }
        }
    }
}
