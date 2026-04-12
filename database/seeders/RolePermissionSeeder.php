<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Gunakan underscore agar sesuai dengan seeder kamu sebelumnya
        $permissions = [
            'create_keputusan', 'edit_keputusan', 'delete_keputusan', 'view_keputusan',
            'create_arahan', 'edit_arahan', 'delete_arahan', 'view_arahan',
            'create_tindak_lanjut', 'edit_tindak_lanjut', 'delete_tindak_lanjut', 'view_tindak_lanjut',
            'approve_stage_1', 'approve_stage_2', 'approve_stage_3', 'approve_stage_4', 'approve_stage_5',
            'view_dashboard', 'export_report', 'manage_users', 'manage_roles', 'manage_unit_kerja', 'view_monitoring',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Roles - Pastikan penamaan sesuai dengan ApprovalController
        $roles = [
            'Admin' => $permissions,
            'Tim Monitoring' => [
                'view_dashboard', 'view_keputusan', 'view_arahan', 
                'view_tindak_lanjut', 'approve_stage_2', 'export_report', 'view_monitoring'
            ],
            'Auditi' => [
                'create_tindak_lanjut', 'edit_tindak_lanjut', 'view_tindak_lanjut'
            ],
            'Atasan Auditi' => ['approve_stage_1', 'view_tindak_lanjut'],
            'Pengendali Teknis' => ['approve_stage_3', 'view_tindak_lanjut'],
            'Pengendali Mutu' => ['approve_stage_4', 'view_tindak_lanjut'],
            'Penanggung Jawab' => ['approve_stage_5', 'view_tindak_lanjut', 'view_dashboard'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
        
        // Buat User Admin Pertama (untuk testing)
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator RUPS',
                'password' => bcrypt('password'),
                'badge' => '000000',
                'status' => 'active'
            ]
        );
        $admin->assignRole('admin');
    }
}