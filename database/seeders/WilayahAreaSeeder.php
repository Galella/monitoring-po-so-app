<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahAreaSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample wilayahs
        $wilayahs = [
            ['name' => 'Wilayah 1 - Jawa Barat', 'code' => 'W1'],
            ['name' => 'Wilayah 2 - Jawa Tengah', 'code' => 'W2'],
            ['name' => 'Wilayah 3 - Jawa Timur', 'code' => 'W3'],
        ];

        foreach ($wilayahs as $wilayahData) {
            Wilayah::firstOrCreate(
                ['code' => $wilayahData['code']],
                $wilayahData
            );
        }

        // Create sample areas
        $areas = [
            // Wilayah 1 areas
            ['name' => 'Area Bandung', 'code' => 'A1-BDG', 'wilayah_code' => 'W1'],
            ['name' => 'Area Bekasi', 'code' => 'A1-BKS', 'wilayah_code' => 'W1'],
            ['name' => 'Area Bogor', 'code' => 'A1-BGR', 'wilayah_code' => 'W1'],
            
            // Wilayah 2 areas
            ['name' => 'Area Semarang', 'code' => 'A2-SMG', 'wilayah_code' => 'W2'],
            ['name' => 'Area Solo', 'code' => 'A2-SLO', 'wilayah_code' => 'W2'],
            
            // Wilayah 3 areas
            ['name' => 'Area Surabaya', 'code' => 'A3-SBY', 'wilayah_code' => 'W3'],
            ['name' => 'Area Malang', 'code' => 'A3-MLG', 'wilayah_code' => 'W3'],
        ];

        foreach ($areas as $areaData) {
            $wilayah = Wilayah::where('code', $areaData['wilayah_code'])->first();
            if ($wilayah) {
                Area::firstOrCreate(
                    ['code' => $areaData['code']],
                    [
                        'name' => $areaData['name'],
                        'code' => $areaData['code'],
                        'wilayah_id' => $wilayah->id,
                    ]
                );
            }
        }
    }
}
