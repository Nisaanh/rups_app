<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
 

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $roles = Role::with('permissions', 'users')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);
        
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalUsersWithRoles = User::has('roles')->count();
        
        return view('roles.index', compact('roles', 'totalRoles', 'totalPermissions', 'totalUsersWithRoles', 'search'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        
        // Group permissions by category
        $groupedPermissions = $this->groupPermissionsByCategory($permissions);
        
        return view('roles.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'Nama role wajib diisi',
            'name.unique' => 'Nama role sudah digunakan',
            'permissions.*.exists' => 'Permission tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create role
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);
            
            // Assign permissions
            if ($request->has('permissions') && !empty($request->permissions)) {
                $role->syncPermissions($request->permissions);
            }

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

           

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', "Role '{$role->name}' berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        
        $usersWithRole = $role->users()->with('unitKerja')->latest()->paginate(10);
        
        $permissionStats = [
            'total' => $role->permissions->count(),
            'by_category' => $this->groupPermissionsByCategory($role->permissions)
        ];
        
        return view('roles.show', compact('role', 'usersWithRole', 'permissionStats'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
{
    // Cek apakah role termasuk yang dilindungi (Admin/Super Admin)
    $isProtected = in_array($role->name, ['admin', 'Admin', 'Super Admin']);
    
    // Ambil semua permission
    $permissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();
    
    // Ambil daftar nama permission yang sudah dimiliki role ini
    $rolePermissions = $role->permissions->pluck('name')->toArray();
    
    return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'isProtected'));
}

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing default Admin role name
        if (in_array($role->name, ['Admin', 'Super Admin']) && $request->name !== $role->name) {
            return redirect()->back()
                ->with('error', "Role '{$role->name}' tidak dapat diubah namanya karena merupakan role sistem.");
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
        DB::beginTransaction();

        // 1. Update nama role (Gunakan update biasa)
        // Note: Spatie Role tidak punya kolom 'description' secara default.
        // Jika Anda menambahkannya lewat migrasi, kode ini sudah benar.
        $role->name = $request->name;
        $role->save(); 
        
        // 2. Sync Permissions
        // Pastikan $request->permissions berisi array NAMA permission (bukan ID)
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            // Jika tidak ada yang dicentang, hapus semua permission dari role ini
            $role->syncPermissions([]);
        }

        // 3. Clear Cache (Penting!)
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        DB::commit();
        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting default system roles
        $protectedRoles = ['Admin', 'Super Admin', 'Tim Monitoring', 'Auditi', 'Atasan Auditi', 'Pengendali Teknis', 'Pengendali Mutu', 'Penanggung Jawab'];
        
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->back()
                ->with('error', "Role '{$role->name}' tidak dapat dihapus karena merupakan role sistem.");
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', "Role '{$role->name}' tidak dapat dihapus karena masih memiliki " . $role->users()->count() . " user terdaftar.");
        }

        try {
            DB::beginTransaction();

            $roleName = $role->name;
            $permissions = $role->permissions->pluck('name')->toArray();
            
            // Delete role
            $role->delete();

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', "Role '{$roleName}' berhasil dihapus.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }

    /**
     * Display permissions management page.
     */
    public function permissions()
    {
        $permissions = Permission::orderBy('name')->get();
        
        // Group permissions by category
        $groupedPermissions = $this->groupPermissionsByCategory($permissions);
        
        $totalPermissions = Permission::count();
        $totalRoles = Role::count();
        
        return view('roles.permissions', compact('groupedPermissions', 'totalPermissions', 'totalRoles'));
    }

    /**
     * Create a new permission.
     */
    public function createPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
            'category' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $permissionName = $request->name;
            
            // Add prefix if category specified
            if ($request->category && !str_contains($request->name, $request->category)) {
                $permissionName = $request->category . '_' . $request->name;
            }
            
            Permission::create([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
            
            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

           

            return redirect()->route('roles.permissions')
                ->with('success', "Permission '{$permissionName}' berhasil dibuat.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat permission: ' . $e->getMessage());
        }
    }

    /**
     * Edit permission.
     */
    public function editPermission(Permission $permission)
    {
        return view('roles.edit-permission', compact('permission'));
    }

    /**
     * Update permission.
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldName = $permission->name;
            $permission->update(['name' => $request->name]);
            
            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

           

            return redirect()->route('roles.permissions')
                ->with('success', "Permission berhasil diupdate.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete permission.
     */
    public function destroyPermission(Permission $permission)
    {
        try {
            // Check if permission is used by any role
            $rolesUsing = $permission->roles()->count();
            
            if ($rolesUsing > 0) {
                return redirect()->back()
                    ->with('error', "Permission '{$permission->name}' tidak dapat dihapus karena masih digunakan oleh {$rolesUsing} role.");
            }
            
            $permissionName = $permission->name;
            $permission->delete();
            
            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

          

            return redirect()->route('roles.permissions')
                ->with('success', "Permission '{$permissionName}' berhasil dihapus.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus permission: ' . $e->getMessage());
        }
    }

    /**
     * Assign role to user.
     */
    public function assignUserRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $oldRoles = $user->getRoleNames()->toArray();
            $newRole = $request->role;
            
            $user->syncRoles([$newRole]);
            
            // Update user role_id field for backward compatibility
            $role = Role::where('name', $newRole)->first();
            $user->update(['role_id' => $role->id]);

            // Clear permission cache for this user
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            

            return redirect()->route('users.edit', $user)
                ->with('success', "Role '{$newRole}' berhasil diassign ke user {$user->name}.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengassign role: ' . $e->getMessage());
        }
    }

    /**
     * Remove role from user.
     */
    public function removeUserRole(User $user)
    {
        try {
            $oldRoles = $user->getRoleNames()->toArray();
            
            $user->syncRoles([]);
            $user->update(['role_id' => null]);

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            

            return redirect()->route('users.edit', $user)
                ->with('success', "Role berhasil dihapus dari user {$user->name}.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }

    /**
     * Display role assignments for users.
     */
    public function userAssignments()
    {
        $users = User::with('roles', 'unitKerja')
            ->whereHas('roles')
            ->orderBy('name')
            ->paginate(20);
            
        $roles = Role::orderBy('name')->get();
        
        return view('roles.user-assignments', compact('users', 'roles'));
    }

    /**
     * Bulk assign roles to users.
     */
    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            $role = Role::where('name', $request->role)->first();
            
            $count = 0;
            foreach ($users as $user) {
                $user->assignRole($request->role);
                $user->update(['role_id' => $role->id]);
                $count++;
            }

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

           
            return redirect()->route('roles.user-assignments')
                ->with('success', "Role '{$request->role}' berhasil diassign ke {$count} user.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal melakukan bulk assign: ' . $e->getMessage());
        }
    }

    /**
     * Refresh permission cache.
     */
    public function refreshCache()
    {
        try {
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            
          

            return redirect()->back()
                ->with('success', 'Cache permission berhasil di-refresh.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal refresh cache: ' . $e->getMessage());
        }
    }

    /**
     * Export roles to CSV.
     */
    public function export()
    {
        $roles = Role::with('permissions', 'users')->get();
        
        $filename = 'roles_export_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add CSV headers
        fputcsv($handle, ['Role Name', 'Permissions', 'Total Users', 'Created At', 'Updated At']);
        
        foreach ($roles as $role) {
            fputcsv($handle, [
                $role->name,
                implode(' | ', $role->permissions->pluck('name')->toArray()),
                $role->users->count(),
                $role->created_at,
                $role->updated_at
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Get statistics about roles and permissions.
     */
    public function statistics()
    {
        $stats = [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users_with_roles' => User::has('roles')->count(),
            'roles_with_most_users' => Role::withCount('users')->orderBy('users_count', 'desc')->take(5)->get(),
            'permissions_most_used' => Permission::withCount('roles')->orderBy('roles_count', 'desc')->take(10)->get(),
            'roles_by_permission_count' => Role::withCount('permissions')->orderBy('permissions_count', 'desc')->get(),
        ];
        
        return view('roles.statistics', compact('stats'));
    }

    /**
     * Group permissions by category.
     */
    private function groupPermissionsByCategory($permissions)
    {
        $categories = [
            'keputusan' => ['name' => 'Keputusan RUPS', 'icon' => '📋', 'color' => 'blue'],
            'arahan' => ['name' => 'Arahan', 'icon' => '📨', 'color' => 'green'],
            'tindak' => ['name' => 'Tindak Lanjut', 'icon' => '✅', 'color' => 'purple'],
            'approve' => ['name' => 'Approval', 'icon' => '🔐', 'color' => 'yellow'],
            'view' => ['name' => 'View & Monitoring', 'icon' => '👁️', 'color' => 'indigo'],
            'export' => ['name' => 'Export & Report', 'icon' => '📊', 'color' => 'orange'],
            'manage' => ['name' => 'Management', 'icon' => '⚙️', 'color' => 'red'],
            'create' => ['name' => 'Create Operations', 'icon' => '➕', 'color' => 'teal'],
            'edit' => ['name' => 'Edit Operations', 'icon' => '✏️', 'color' => 'cyan'],
            'delete' => ['name' => 'Delete Operations', 'icon' => '🗑️', 'color' => 'rose'],
        ];
        
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $category = 'other';
            
            foreach (array_keys($categories) as $cat) {
                if (str_contains($permission->name, $cat)) {
                    $category = $cat;
                    break;
                }
            }
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'info' => $categories[$category] ?? ['name' => 'Lainnya', 'icon' => '📁', 'color' => 'gray'],
                    'permissions' => []
                ];
            }
            
            $grouped[$category]['permissions'][] = $permission;
        }
        
        // Sort by category priority
        $orderedCategories = array_keys($categories);
        uksort($grouped, function($a, $b) use ($orderedCategories) {
            $posA = array_search($a, $orderedCategories);
            $posB = array_search($b, $orderedCategories);
            if ($posA === false) $posA = 999;
            if ($posB === false) $posB = 999;
            return $posA - $posB;
        });
        
        return $grouped;
    }
}