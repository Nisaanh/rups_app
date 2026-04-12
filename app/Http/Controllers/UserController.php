<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UnitKerja;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $unit = $request->query('unit_kerja_id');
        $status = $request->query('status', 'active');

        $users = User::with('unitKerja', 'roles', 'picUnit')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('badge', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->role($role);
            })
            ->when($unit, function ($query, $unit) {
                return $query->where('unit_kerja_id', $unit);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        // Get data for filters
        $roles = Role::orderBy('name')->get();
        $unitKerja = UnitKerja::orderBy('level')->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'by_role' => DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(*) as total'))
                ->groupBy('roles.name')
                ->pluck('total', 'name')
                ->toArray(),
            'by_unit' => User::whereNotNull('unit_kerja_id')
                ->select('unit_kerja_id', DB::raw('count(*) as total'))
                ->groupBy('unit_kerja_id')
                ->with('unitKerja')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->unitKerja->name => $item->total];
                })
                ->toArray(),
        ];

        return view('users.index', compact('users', 'roles', 'unitKerja', 'stats', 'search', 'role', 'unit', 'status'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $unitKerja = UnitKerja::orderBy('level')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Kita ambil user yang punya role otoritas (Admin, Monitoring, Atasan Auditi) 
        // dan kita Eager Load 'unitKerja' supaya bisa di-grouping di Blade.
        $picUsers = User::role(['Admin', 'Tim Monitoring', 'Atasan Auditi'])
            ->where('status', 'active')
            ->with('unitKerja')
            ->orderBy('name')
            ->get();

        return view('users.create', compact('unitKerja', 'roles', 'picUsers'));
    }

    /**
     * Store a newly created user in storage.
     */
  public function store(Request $request)
{
    // 1. Validasi Data
    $request->validate([
        'badge' => 'required|string|max:50|unique:users,badge',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
        'pic_unit_kerja_id' => 'nullable|exists:users,id',
        'role' => 'required|exists:roles,name',
        'status' => 'required|in:active,inactive'
    ]);

    try {
        DB::beginTransaction();

        // 2. Buat User (Tanpa role_id karena tidak ada di DB kamu)
        $user = User::create([
            'badge' => $request->badge,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_kerja_id' => $request->unit_kerja_id,
            'pic_unit_kerja_id' => $request->pic_unit_kerja_id,
            'status' => $request->status,
        ]);

        // 3. Pasang Role menggunakan Spatie (Ini akan masuk ke tabel model_has_roles)
        $user->assignRole($request->role);
        
        // JANGAN gunakan $user->update(['role_id' => ...]) karena kolomnya tidak ada di DB kamu

        DB::commit();

        // Hapus Cache agar data terbaru muncul
        Cache::forget('users_list');

        return redirect()->route('users.index')->with('success', 'User ' . $user->name . ' berhasil ditambahkan!');

    } catch (\Exception $e) {
        DB::rollBack();
        // Munculkan error spesifik jika gagal
        return back()->with('error', 'Gagal Simpan: ' . $e->getMessage())->withInput();
    }
}

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load([
            'unitKerja',
            'picUnit',
            'roles',
            'permissions',
            'keputusan',
            'arahanPic',
            'tindakLanjut',
            'approvals'
        ]);

        // Get user statistics
        $stats = [
            'total_keputusan' => $user->keputusan()->count(),
            'total_arahan' => $user->arahanPic()->count(),
            'total_tindak_lanjut' => $user->tindakLanjut()->count(),
            'total_approvals' => $user->approvals()->count(),
            'approved_approvals' => $user->approvals()->where('status', 'approved')->count(),
            'rejected_approvals' => $user->approvals()->where('status', 'rejected')->count(),
            'pending_approvals' => $user->approvals()->where('status', 'pending')->count(),
        ];


        // Get subordinates (users where pic_unit_kerja_id = this user)
        $subordinates = User::where('pic_unit_kerja_id', $user->id)
            ->with('unitKerja', 'roles')
            ->get();

        return view('users.show', compact('user', 'stats', 'subordinates'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $unitKerja = UnitKerja::orderBy('level')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Sama seperti create, ambil calon PIC otoritas
        $picUsers = User::role(['Admin', 'Tim Monitoring', 'Atasan Auditi'])
            ->where('status', 'active')
            ->where('id', '!=', $user->id) // Jangan sampai dia jadi atasan dirinya sendiri
            ->with('unitKerja')
            ->orderBy('name')
            ->get();

        $userRole = $user->roles->first();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return view('users.edit', compact('user', 'unitKerja', 'roles', 'picUsers', 'userRole', 'userPermissions'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'badge' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'pic_unit_kerja_id' => 'nullable|exists:users,id',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive'
        ], [
            'badge.required' => 'Badge/NIP wajib diisi',
            'badge.unique' => 'Badge/NIP sudah digunakan',
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah digunakan',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'role.required' => 'Role wajib dipilih'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $oldData = $user->toArray();
            $oldRole = $user->roles->first() ? $user->roles->first()->name : null;

            // Update user data
            $updateData = [
                'badge' => $request->badge,
                'name' => $request->name,
                'email' => $request->email,
                'unit_kerja_id' => $request->unit_kerja_id,
                'pic_unit_kerja_id' => $request->pic_unit_kerja_id,
                'status' => $request->status
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Update role if changed
            if ($oldRole !== $request->role) {
                $role = Role::findByName($request->role);
                $user->syncRoles([$role]);
                // $user->update(['role_id' => $role->id]);
            }

            // Clear user cache
            Cache::forget('users_list');
            Cache::forget("user_{$user->id}_permissions");

            // Log activity
            $changes = [];
            if ($oldData['name'] != $request->name) {
                $changes['name'] = ['old' => $oldData['name'], 'new' => $request->name];
            }
            if ($oldData['badge'] != $request->badge) {
                $changes['badge'] = ['old' => $oldData['badge'], 'new' => $request->badge];
            }
            if ($oldData['email'] != $request->email) {
                $changes['email'] = ['old' => $oldData['email'], 'new' => $request->email];
            }
            if ($oldData['status'] != $request->status) {
                $changes['status'] = ['old' => $oldData['status'], 'new' => $request->status];
            }
            if ($oldRole !== $request->role) {
                $changes['role'] = ['old' => $oldRole, 'new' => $request->role];
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', "User '{$user->name}' berhasil diupdate.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // Check if user has related data
            $hasKeputusan = $user->keputusan()->exists();
            $hasArahan = $user->arahanPic()->exists();
            $hasTindakLanjut = $user->tindakLanjut()->exists();
            $hasApprovals = $user->approvals()->exists();

            if ($hasKeputusan || $hasArahan || $hasTindakLanjut || $hasApprovals) {
                $messages = [];
                if ($hasKeputusan) $messages[] = $user->keputusan()->count() . ' keputusan';
                if ($hasArahan) $messages[] = $user->arahanPic()->count() . ' arahan';
                if ($hasTindakLanjut) $messages[] = $user->tindakLanjut()->count() . ' tindak lanjut';
                if ($hasApprovals) $messages[] = $user->approvals()->count() . ' approval';

                return redirect()->back()
                    ->with('error', "User '{$user->name}' tidak dapat dihapus karena masih memiliki data terkait: " . implode(', ', $messages));
            }

            // Check if user is PIC for other users
            $hasSubordinates = User::where('pic_unit_kerja_id', $user->id)->exists();
            if ($hasSubordinates) {
                return redirect()->back()
                    ->with('error', "User '{$user->name}' tidak dapat dihapus karena masih menjadi atasan bagi user lain.");
            }

            $userName = $user->name;
            $userBadge = $user->badge;

            // Remove roles
            $user->syncRoles([]);

            // Delete user
            $user->delete();

            // Clear cache
            Cache::forget('users_list');
            Cache::forget("user_{$user->id}_permissions");

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', "User '{$userName}' berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete users.
     */
    public function bulkDestroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $deleted = 0;
            $errors = [];

            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);

                // Skip if user has related data
                if (
                    $user->keputusan()->exists() ||
                    $user->arahanPic()->exists() ||
                    $user->tindakLanjut()->exists() ||
                    $user->approvals()->exists() ||
                    User::where('pic_unit_kerja_id', $userId)->exists()
                ) {
                    $errors[] = $user->name;
                    continue;
                }

                $user->syncRoles([]);
                $user->delete();
                $deleted++;
            }

            // Clear cache
            Cache::forget('users_list');

            DB::commit();

            $message = "Berhasil menghapus {$deleted} user.";
            if (!empty($errors)) {
                $message .= " Gagal menghapus: " . implode(', ', $errors);
            }

            return redirect()->route('users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal melakukan bulk delete: ' . $e->getMessage());
        }
    }

    /**
     * Change user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        try {
            DB::beginTransaction();

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            // Clear cache
            Cache::forget('users_list');
            Cache::forget("user_{$user->id}_permissions");

            DB::commit();

            $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()
                ->with('success', "User '{$user->name}' berhasil {$statusText}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $user->update(['password' => Hash::make($request->password)]);

            DB::commit();

            return redirect()->back()
                ->with('success', "Password user '{$user->name}' berhasil direset.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }

    /**
     * Export users to CSV/Excel.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $role = $request->get('role');
        $unit = $request->get('unit');
        $status = $request->get('status');

        $users = User::with('unitKerja', 'roles')
            ->when($role, function ($query, $role) {
                return $query->role($role);
            })
            ->when($unit, function ($query, $unit) {
                return $query->where('unit_kerja_id', $unit);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('name')
            ->get();

        if ($format === 'csv') {
            $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
            $handle = fopen('php://temp', 'w+');

            // CSV Headers
            fputcsv($handle, ['Badge/NIP', 'Nama Lengkap', 'Email', 'Unit Kerja', 'Role', 'Atasan', 'Status', 'Terakhir Login', 'Dibuat']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->badge,
                    $user->name,
                    $user->email,
                    $user->unitKerja ? $user->unitKerja->name : '-',
                    $user->roles->first() ? $user->roles->first()->name : '-',
                    $user->picUnit ? $user->picUnit->name : '-',
                    $user->status === 'active' ? 'Aktif' : 'Nonaktif',
                    $user->last_login_at ? date('d/m/Y H:i', strtotime($user->last_login_at)) : '-',
                    $user->created_at->format('d/m/Y H:i')
                ]);
            }

            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response($csv, 200)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename={$filename}");
        }

        return redirect()->back()->with('error', 'Format export tidak didukung.');
    }

    /**
     * Import users from CSV.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');

            $header = fgetcsv($handle);
            $imported = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== FALSE) {
                $data = array_combine($header, $row);

                // Validate required fields
                if (empty($data['badge']) || empty($data['name']) || empty($data['email'])) {
                    $errors[] = "Data tidak lengkap: " . json_encode($data);
                    continue;
                }

                // Check if user exists
                if (User::where('badge', $data['badge'])->orWhere('email', $data['email'])->exists()) {
                    $errors[] = "User dengan badge '{$data['badge']}' atau email '{$data['email']}' sudah ada";
                    continue;
                }

                // Find unit kerja
                $unitKerjaId = null;
                if (!empty($data['unit_kerja'])) {
                    $unit = UnitKerja::where('name', $data['unit_kerja'])->first();
                    if ($unit) {
                        $unitKerjaId = $unit->id;
                    } else {
                        $errors[] = "Unit kerja '{$data['unit_kerja']}' tidak ditemukan untuk user '{$data['name']}'";
                        continue;
                    }
                }

                // Find role
                $role = Role::where('name', $data['role'] ?? 'Auditi')->first();
                if (!$role) {
                    $errors[] = "Role '{$data['role']}' tidak ditemukan untuk user '{$data['name']}'";
                    continue;
                }

                // Create user
                $user = User::create([
                    'badge' => $data['badge'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'] ?? 'password123'),
                    'unit_kerja_id' => $unitKerjaId,
                    'status' => $data['status'] ?? 'active'
                ]);

                $user->assignRole($role);
                $user->update(['role_id' => $role->id]);

                $imported++;
            }

            fclose($handle);

            // Clear cache
            Cache::forget('users_list');

            $message = "Berhasil mengimport {$imported} user.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " error.";
            }

            return redirect()->route('users.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import file: ' . $e->getMessage());
        }
    }

    /**
     * Search users (for AJAX).
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $role = $request->get('role');
        $unit = $request->get('unit');

        $users = User::where('status', 'active')
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('badge', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($role, function ($q) use ($role) {
                return $q->role($role);
            })
            ->when($unit, function ($q) use ($unit) {
                return $q->where('unit_kerja_id', $unit);
            })
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'badge' => $user->badge,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                    'unit_kerja' => $user->unitKerja ? $user->unitKerja->name : null
                ];
            });

        return response()->json($users);
    }

    /**
     * Get user statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'by_role' => DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(*) as total'))
                ->groupBy('roles.name')
                ->get(),
            'by_unit' => User::whereNotNull('unit_kerja_id')
                ->select('unit_kerja_id', DB::raw('count(*) as total'))
                ->groupBy('unit_kerja_id')
                ->with('unitKerja')
                ->get()
                ->map(function ($item) {
                    return [
                        'unit' => $item->unitKerja ? $item->unitKerja->name : 'Unknown',
                        'total' => $item->total
                    ];
                }),
            'recent_users' => User::latest()->take(10)->get(),

        ];

        return view('users.statistics', compact('stats'));
    }

    /**
     * Get user profile.
     */
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        // Load relasi agar data lengkap
        $user->load(['unitKerja', 'roles', 'permissions']);

        $stats = [
            'total_keputusan' => $user->keputusan()->count(),
            'total_arahan' => $user->arahanPic()->count(),
            'total_tindak_lanjut' => $user->tindakLanjut()->count(),
            'total_approvals' => $user->approvals()->count(),
        ];


        return view('users.profile', compact('user', 'stats'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        // Pastikan Rule di-import di atas: use Illuminate\Validation\Rule;
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email
            ];

            // Menggunakan Hash::make (Pastikan import: use Illuminate\Support\Facades\Hash;)
            if ($request->filled('password')) {
                $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $user->update($updateData);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
   public function getPicByUnit($unitId)
{
    $unit = UnitKerja::find($unitId);

    if (!$unit) {
        return response()->json([]);
    }

    // Ambil unit sendiri
    $unitIds = [$unit->id];

    // Ambil parent dari report_to (INI YANG BENAR)
    if ($unit->report_to) {
        $unitIds[] = $unit->report_to;
    }

    // Cari user yang bisa jadi PIC
    $users = User::whereIn('unit_kerja_id', $unitIds)
        ->where('status', 'active')
        ->whereHas('roles', function ($q) {
            $q->whereIn('name', ['Atasan Auditi', 'Tim Monitoring', 'Admin']);
        })
        ->get();

    return response()->json($users);
}
}
