<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $direktorat = UnitKerja::create([
            'name' => 'Direktorat Utama',
            'level' => 'Direktorat',
            'report_to' => null
        ]);

        $divisi = UnitKerja::create([
            'name' => 'Divisi Operasional',
            'level' => 'Divisi',
            'report_to' => $direktorat->id
        ]);

        UnitKerja::create([
            'name' => 'Departemen IT',
            'level' => 'Departemen',
            'report_to' => $divisi->id
        ]);

        UnitKerja::create([
            'name' => 'Departemen Keuangan',
            'level' => 'Departemen',
            'report_to' => $divisi->id
        ]);
    }
}