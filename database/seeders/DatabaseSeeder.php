<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed pekerjaans
        $pekerjaans = [
            'Dokter',
            'Perawat',
            'Guru',
            'Pegawai Negeri Sipil',
            'Karyawan Swasta',
            'Wiraswasta',
            'Petani',
            'Buruh',
            'Mahasiswa',
            'Ibu Rumah Tangga',
            'Pensiunan',
            'Lainnya'
        ];

        foreach ($pekerjaans as $pekerjaan) {
            Pekerjaan::create([
                'nama_pekerjaan' => $pekerjaan
            ]);
        }
    }
}