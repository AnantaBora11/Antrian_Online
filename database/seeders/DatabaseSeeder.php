<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Layanan;
use App\Models\Loket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@antrian.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Buat petugas
        $petugas1 = User::create([
            'name' => 'Petugas 1',
            'email' => 'petugas1@antrian.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        $petugas2 = User::create([
            'name' => 'Petugas 2',
            'email' => 'petugas2@antrian.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        $petugas3 = User::create([
            'name' => 'Petugas 3',
            'email' => 'petugas3@antrian.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        // Buat layanan
        $poliUmum = Layanan::create([
            'kode' => 'A',
            'nama' => 'Poli Umum',
            'deskripsi' => 'Layanan kesehatan umum untuk pemeriksaan dan konsultasi.',
        ]);

        $poliGigi = Layanan::create([
            'kode' => 'B',
            'nama' => 'Poli Gigi',
            'deskripsi' => 'Layanan kesehatan gigi dan mulut.',
        ]);

        $farmasi = Layanan::create([
            'kode' => 'C',
            'nama' => 'Farmasi',
            'deskripsi' => 'Layanan pengambilan obat dan resep.',
        ]);

        // Buat loket
        Loket::create([
            'nama' => 'Loket 1',
            'layanan_id' => $poliUmum->id,
            'user_id' => $petugas1->id,
        ]);

        Loket::create([
            'nama' => 'Loket 2',
            'layanan_id' => $poliGigi->id,
            'user_id' => $petugas2->id,
        ]);

        Loket::create([
            'nama' => 'Loket 3',
            'layanan_id' => $farmasi->id,
            'user_id' => $petugas3->id,
        ]);
    }
}
