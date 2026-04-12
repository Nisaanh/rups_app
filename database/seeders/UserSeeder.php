<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UnitKerja;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $unitKerja = UnitKerja::first();

        // Admin
        $admin = User::create([
            'badge' => 'ADM001',
            'name' => 'Admin Sistem',
            'email' => 'admin@rups.com',
            'password' => Hash::make('password'),
            'unit_kerja_id' => $unitKerja->id,
            'status' => 'active'
        ]);
        $admin->assignRole('Admin');

        // Tim Monitoring
        $timMon = User::create([
            'badge' => 'TM001',
            'name' => 'Tim Monitoring',
            'email' => 'timmon@rups.com',
            'password' => Hash::make('password'),
            'unit_kerja_id' => $unitKerja->id,
            'status' => 'active'
        ]);
        $timMon->assignRole('Tim Monitoring');

        // Auditi
        $auditi = User::create([
            'badge' => 'AUD001',
            'name' => 'Auditi User',
            'email' => 'auditi@rups.com',
            'password' => Hash::make('password'),
            'unit_kerja_id' => $unitKerja->id,
            'status' => 'active'
        ]);
        $auditi->assignRole('Auditi');

        // Atasan Auditi
        $atasan = User::create([
            'badge' => 'ATA001',
            'name' => 'Atasan Auditi',
            'email' => 'atasan@rups.com',
            'password' => Hash::make('password'),
            'unit_kerja_id' => $unitKerja->id,
            'status' => 'active'
        ]);
        $atasan->assignRole('Atasan Auditi');
    }
}