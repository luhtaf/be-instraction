<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            ['nama' => 'SEKRETARIAT UTAMA'],
            ['nama' => 'DEPUTI 1'],
            ['nama' => 'DEPUTI 2'],
            ['nama' => 'DEPUTI 3'],
            ['nama' => 'DEPUTI 4'],
            ['nama' => 'INSPEKTORAT'],
            ['nama' => 'PUSSERTIF (P1)'],
            ['nama' => 'PUSDATIK (P2)'],
            ['nama' => 'PUSBANGSDM (P3)'],
            ['nama' => 'POLTEKSSN'],
            ['nama' => 'BIRO RENKEU (S1)'],
            ['nama' => 'BIRO OSDM (S2)'],
            ['nama' => 'BIRO KUMKOMLIK (S3)'],
            ['nama' => 'BIRO UMUM (S4)'],
            ['nama' => 'DIT STRATEGI KSS (D11)'],
            ['nama' => 'DIT TATA KELOLA KSS (D12)'],
            ['nama' => 'DIT TEKNOLOGI KSS (D13)'],
            ['nama' => 'DIT SDM KSS (D14)'],
            ['nama' => 'DIT OPSKAMSIBER (D21)', 'parent' => 'DEPUTI 2'],
            ['nama' => 'DIT OPSKAMDALINFO (D22)', 'parent' => 'DEPUTI 2'],
            ['nama' => 'DIT OPSAN (D23)', 'parent' => 'DEPUTI 2'],
            ['nama' => 'DIT KAMSISAN PEMPUS (D31)'],
            ['nama' => 'DIT KAMSISAN PEMDA (D32)'],
            ['nama' => 'DIT KAMSISAN BANGMAN (D33)'],
            ['nama' => 'DIT KSS KPP (D41)'],
            ['nama' => 'DIT KSS ESDA (D42)'],
            ['nama' => 'DIT KSS TIKMT (D43)'],
            ['nama' => 'DIT KSS INDUSTRI (D44)'],
            ['nama' => 'BSRE'],
            ['nama' => 'BDS'],
            ['nama' => 'JUBIR'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
