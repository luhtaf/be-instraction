<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        // Data Karyawan
        $karyawanData = [
            [
                'nama' => 'AGUNG KURNIAWAN PURBOHADI, S.E., Ak., M.M.',
                'email' => 'agung.kurniawan@company.com',
                'jabatan' => 'Kepala Biro Perencanaan dan Keuangan, Sekretariat Utama',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'VIRGO RITA KURNIASIH, M.E.',
                'email' => 'virgo.kurniasih@company.com',
                'jabatan' => 'Perencana Ahli Madya',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'BAYU HARYANTO, S.E.',
                'email' => 'bayu.haryanto@company.com',
                'jabatan' => 'Analis Pengelolaan Keuangan APBN Ahli Madya',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'DECKY JULIANDARSONO, S.ST, M.M.',
                'email' => 'decky.juliandarsono@company.com',
                'jabatan' => 'Analis Anggaran Muda',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'ELIZABETH IMELDA YANI, S.AP., M.M.',
                'email' => 'elizabeth.imelda@company.com',
                'jabatan' => 'Perencana Ahli Muda',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'FAJAR REZTOSA PRATAMA, S.ST',
                'email' => 'fajar.reztosa@company.com',
                'jabatan' => 'Analis Anggaran Muda',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'FARIDA YULISTIA, S.E.',
                'email' => 'farida.yulistia@company.com',
                'jabatan' => 'Perencana Ahli Muda',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
            [
                'nama' => 'NITIA RAHAJENG, S.ST,. M.M.',
                'email' => 'nitia.rahajeng@company.com',
                'jabatan' => 'Perencana Ahli Muda',
                'unit_kerja' => 'Biro Perencanaan dan Keuangan, Sekretariat Utama',
            ],
        ];

        // Menambahkan data ke tabel Karyawan
        foreach ($karyawanData as $data) {
            Karyawan::create($data);
        }
    }
}
