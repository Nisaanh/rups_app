<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class UnitKerjaController extends Controller
{
   
    /**
     * Display a listing of unit kerja with hierarchical structure.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $level = $request->get('level');
        
        $unitKerja = UnitKerja::with('parent', 'children', 'users')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->when($level, function ($query, $level) {
                return $query->where('level', $level);
            })
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(15);
        
        // Get hierarchical tree for sidebar/select
        $tree = $this->buildHierarchyTree();
        
        // Statistics
        $stats = [
            'total' => UnitKerja::count(),
            'by_level' => UnitKerja::select('level', DB::raw('count(*) as total'))
                ->groupBy('level')
                ->pluck('total', 'level')
                ->toArray(),
            'total_users' => User::whereNotNull('unit_kerja_id')->count(),
            'total_arahan' => \App\Models\Arahan::count(),
            'total_tindak_lanjut' => \App\Models\TindakLanjut::count(),
        ];
        
        $levels = UnitKerja::distinct()->pluck('level');
        
        return view('unit-kerja.index', compact('unitKerja', 'tree', 'stats', 'levels', 'search', 'level'));
    }

    /**
     * Show the form for creating a new unit kerja.
     */
   /**
 * Show the form for creating a new unit kerja.
 */
public function create()
{
    // 1. Ambil semua unit kerja yang sudah ada untuk jadi pilihan Atasan (Parent)
    // Kita namakan variabelnya $parents agar sesuai dengan looping di Blade kamu
    $parents = UnitKerja::orderBy('name')->get();
    
    // 2. Data pendukung lainnya
    $tree = $this->buildHierarchyTree();
    $levels = ['Direktorat', 'Divisi', 'Departemen', 'Seksi', 'Sub Seksi'];
    
    // 3. Kirim ke view (Pastikan ada 'parents' di dalam compact)
    return view('unit-kerja.create', compact('parents', 'tree', 'levels'));
}

    /**
     * Store a newly created unit kerja in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:unit_kerja,name',
            'level' => 'required|in:Direktorat,Divisi,Departemen,Seksi,Sub Seksi',
            'report_to' => 'nullable|exists:unit_kerja,id'
        ], [
            'name.required' => 'Nama unit kerja wajib diisi',
            'name.unique' => 'Nama unit kerja sudah digunakan',
            'level.required' => 'Level unit kerja wajib dipilih',
            'level.in' => 'Level unit kerja tidak valid',
            'report_to.exists' => 'Unit kerja atasan tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Validate hierarchy (cannot report to lower level)
            if ($request->report_to) {
                $parent = UnitKerja::find($request->report_to);
                $currentLevelOrder = $this->getLevelOrder($request->level);
                $parentLevelOrder = $this->getLevelOrder($parent->level);
                
                if ($currentLevelOrder <= $parentLevelOrder) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Unit kerja tidak dapat melapor ke unit dengan level yang sama atau lebih rendah.')
                        ->withInput();
                }
            }

            $unitKerja = UnitKerja::create([
                'name' => $request->name,
                'level' => $request->level,
                'report_to' => $request->report_to
            ]);

            // Clear hierarchy cache
            Cache::forget('unit_kerja_hierarchy');
            Cache::forget('unit_kerja_tree');
            DB::commit();

            return redirect()->route('unit-kerja.index')
                ->with('success', "Unit kerja '{$unitKerja->name}' berhasil ditambahkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan unit kerja: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified unit kerja.
     */
    public function show(UnitKerja $unitKerja)
    {
        $unitKerja->load(['parent', 'children', 'users', 'arahan.keputusan', 'tindakLanjut']);
        
        // Get children recursively
        $descendants = $this->getDescendants($unitKerja->id);
        
        // Get statistics
        $stats = [
            'total_users' => $unitKerja->users()->count(),
            'total_children' => $unitKerja->children()->count(),
            'total_descendants' => count($descendants),
            'total_arahan' => $unitKerja->arahan()->count(),
            'total_tindak_lanjut' => $unitKerja->tindakLanjut()->count(),
            'pending_approvals' => $unitKerja->tindakLanjut()->where('status', 'pending')->count(),
            'completed_actions' => $unitKerja->tindakLanjut()->where('status', 'approved')->count(),
        ];
        
       
        return view('unit-kerja.show', compact('unitKerja', 'descendants', 'stats'));
    }

    /**
     * Show the form for editing the specified unit kerja.
     */
    public function edit(UnitKerja $unitKerja)
    {
        $allUnitKerja = UnitKerja::where('id', '!=', $unitKerja->id)->get();
        $tree = $this->buildHierarchyTree($unitKerja->id);
        $levels = ['Direktorat', 'Divisi', 'Departemen', 'Seksi', 'Sub Seksi'];
        
        // Check if unit has children
        $hasChildren = $unitKerja->children()->exists();
         $parents = $allUnitKerja;
        
        return view('unit-kerja.edit', compact('unitKerja', 'allUnitKerja', 'tree', 'levels', 'hasChildren','parents'));
    }

    /**
     * Update the specified unit kerja in storage.
     */
    public function update(Request $request, UnitKerja $unitKerja)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:unit_kerja,name,' . $unitKerja->id,
            'level' => 'required|in:Direktorat,Divisi,Departemen,Seksi,Sub Seksi',
            'report_to' => 'nullable|exists:unit_kerja,id|different:' . $unitKerja->id
        ], [
            'name.required' => 'Nama unit kerja wajib diisi',
            'name.unique' => 'Nama unit kerja sudah digunakan',
            'level.required' => 'Level unit kerja wajib dipilih',
            'report_to.different' => 'Unit kerja tidak dapat melapor ke dirinya sendiri'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $oldData = $unitKerja->toArray();

            // Validate hierarchy constraints
            if ($request->report_to) {
                $parent = UnitKerja::find($request->report_to);
                
                // Prevent circular reference
                if ($this->isCircularReference($unitKerja->id, $request->report_to)) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Tidak dapat mengubah struktur karena akan menyebabkan referensi melingkar.')
                        ->withInput();
                }
                
                // Validate level hierarchy
                $currentLevelOrder = $this->getLevelOrder($request->level);
                $parentLevelOrder = $this->getLevelOrder($parent->level);
                
                if ($currentLevelOrder <= $parentLevelOrder) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Unit kerja tidak dapat melapor ke unit dengan level yang sama atau lebih rendah.')
                        ->withInput();
                }
                
                // Cannot change parent if has children at same level
                if ($unitKerja->children()->exists() && $unitKerja->report_to != $request->report_to) {
                    $affectedChildren = $unitKerja->children()->where('level', $unitKerja->level)->count();
                    if ($affectedChildren > 0) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', "Tidak dapat mengubah atasan karena masih ada {$affectedChildren} unit kerja bawahan dengan level yang sama.")
                            ->withInput();
                    }
                }
            }

            $unitKerja->update([
                'name' => $request->name,
                'level' => $request->level,
                'report_to' => $request->report_to
            ]);

            // Clear cache
            Cache::forget('unit_kerja_hierarchy');
            Cache::forget('unit_kerja_tree');

            // Log activity
            $changes = [];
            if ($oldData['name'] != $request->name) {
                $changes['name'] = ['old' => $oldData['name'], 'new' => $request->name];
            }
            if ($oldData['level'] != $request->level) {
                $changes['level'] = ['old' => $oldData['level'], 'new' => $request->level];
            }
            if ($oldData['report_to'] != $request->report_to) {
                $changes['report_to'] = ['old' => $oldData['report_to'], 'new' => $request->report_to];
            }

            DB::commit();

            return redirect()->route('unit-kerja.index')
                ->with('success', "Unit kerja '{$unitKerja->name}' berhasil diupdate.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengupdate unit kerja: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified unit kerja from storage.
     */
    public function destroy(UnitKerja $unitKerja)
    {
        try {
            DB::beginTransaction();

            // Check if unit has children
            if ($unitKerja->children()->count() > 0) {
                return redirect()->back()
                    ->with('error', "Unit kerja '{$unitKerja->name}' tidak dapat dihapus karena masih memiliki " . $unitKerja->children()->count() . " unit kerja bawahan.");
            }

            // Check if unit has users
            if ($unitKerja->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', "Unit kerja '{$unitKerja->name}' tidak dapat dihapus karena masih memiliki " . $unitKerja->users()->count() . " user terdaftar.");
            }

            // Check if unit has arahan
            if ($unitKerja->arahan()->count() > 0) {
                return redirect()->back()
                    ->with('error', "Unit kerja '{$unitKerja->name}' tidak dapat dihapus karena masih memiliki " . $unitKerja->arahan()->count() . " arahan terkait.");
            }

            // Check if unit has tindak lanjut
            if ($unitKerja->tindakLanjut()->count() > 0) {
                return redirect()->back()
                    ->with('error', "Unit kerja '{$unitKerja->name}' tidak dapat dihapus karena masih memiliki " . $unitKerja->tindakLanjut()->count() . " tindak lanjut terkait.");
            }

            $unitName = $unitKerja->name;
            $unitLevel = $unitKerja->level;
            
            $unitKerja->delete();

            // Clear cache
            Cache::forget('unit_kerja_hierarchy');
            Cache::forget('unit_kerja_tree');

            DB::commit();

            return redirect()->route('unit-kerja.index')
                ->with('success', "Unit kerja '{$unitName}' berhasil dihapus.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus unit kerja: ' . $e->getMessage());
        }
    }

    /**
     * Get users by unit kerja (for API/JSON).
     */
    public function getUsers(UnitKerja $unitKerja)
    {
        $users = $unitKerja->users()
            ->with('roles')
            ->where('status', 'active')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'badge' => $user->badge,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                    'status' => $user->status
                ];
            });
        
        return response()->json([
            'success' => true,
            'unit_kerja' => $unitKerja->name,
            'users' => $users
        ]);
    }

    /**
     * Get unit kerja tree structure (for API/JSON).
     */
    public function getTree()
    {
        $tree = $this->buildHierarchyTree();
        
        return response()->json([
            'success' => true,
            'tree' => $tree
        ]);
    }

    /**
     * Export unit kerja to CSV/Excel.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $unitKerja = UnitKerja::with('parent', 'users')
            ->orderBy('level')
            ->orderBy('name')
            ->get();
        
        if ($format === 'csv') {
            $filename = 'unit_kerja_export_' . date('Y-m-d_His') . '.csv';
            $handle = fopen('php://temp', 'w+');
            
            // CSV Headers
            fputcsv($handle, ['ID', 'Nama Unit Kerja', 'Level', 'Atasan', 'Jumlah User', 'Jumlah Arahan', 'Jumlah Tindak Lanjut', 'Dibuat', 'Diupdate']);
            
            foreach ($unitKerja as $unit) {
                fputcsv($handle, [
                    $unit->id,
                    $unit->name,
                    $unit->level,
                    $unit->parent ? $unit->parent->name : '-',
                    $unit->users->count(),
                    $unit->arahan()->count(),
                    $unit->tindakLanjut()->count(),
                    $unit->created_at,
                    $unit->updated_at
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
     * Bulk import unit kerja from CSV.
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
                
                // Validate and create unit kerja
                $unitData = [
                    'name' => $data['name'] ?? null,
                    'level' => $data['level'] ?? null,
                    'report_to' => null
                ];
                
                // Find parent by name if exists
                if (isset($data['parent_name']) && $data['parent_name']) {
                    $parent = UnitKerja::where('name', $data['parent_name'])->first();
                    if ($parent) {
                        $unitData['report_to'] = $parent->id;
                    } else {
                        $errors[] = "Parent '{$data['parent_name']}' not found for unit '{$data['name']}'";
                        continue;
                    }
                }
                
                $unit = UnitKerja::create($unitData);
                $imported++;
            }
            
            fclose($handle);
            
            // Clear cache
            Cache::forget('unit_kerja_hierarchy');
            Cache::forget('unit_kerja_tree');
            
           
            
            $message = "Berhasil mengimport {$imported} unit kerja.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " error.";
            }
            
            return redirect()->route('unit-kerja.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import file: ' . $e->getMessage());
        }
    }

    /**
     * Reorder unit kerja hierarchy.
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:unit_kerja,id',
            'items.*.parent_id' => 'nullable|exists:unit_kerja,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            
            foreach ($request->items as $item) {
                $unit = UnitKerja::find($item['id']);
                $unit->report_to = $item['parent_id'] ?? null;
                $unit->save();
            }
            
            // Clear cache
            Cache::forget('unit_kerja_hierarchy');
            Cache::forget('unit_kerja_tree');
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Hierarki berhasil diupdate']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => UnitKerja::count(),
            'by_level' => [
                'Direktorat' => UnitKerja::where('level', 'Direktorat')->count(),
                'Divisi' => UnitKerja::where('level', 'Divisi')->count(),
                'Departemen' => UnitKerja::where('level', 'Departemen')->count(),
                'Seksi' => UnitKerja::where('level', 'Seksi')->count(),
                'Sub Seksi' => UnitKerja::where('level', 'Sub Seksi')->count(),
            ],
            'hierarchy_depth' => $this->getMaxHierarchyDepth(),
            'units_without_parent' => UnitKerja::whereNull('report_to')->count(),
            'units_with_children' => UnitKerja::has('children')->count(),
            'most_users' => UnitKerja::withCount('users')->orderBy('users_count', 'desc')->take(5)->get(),
            'most_arahan' => UnitKerja::withCount('arahan')->orderBy('arahan_count', 'desc')->take(5)->get(),
            'most_tindak_lanjut' => UnitKerja::withCount('tindakLanjut')->orderBy('tindak_lanjut_count', 'desc')->take(5)->get(),
        ];
        
        return view('unit-kerja.statistics', compact('stats'));
    }

    /**
     * Search unit kerja by name.
     */
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        $results = UnitKerja::where('name', 'like', "%{$query}%")
            ->orWhere('level', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'level' => $unit->level,
                    'parent' => $unit->parent ? $unit->parent->name : null,
                    'users_count' => $unit->users()->count()
                ];
            });
        
        return response()->json($results);
    }

    /**
     * Build hierarchical tree of unit kerja.
     */
    private function buildHierarchyTree($excludeId = null)
    {
        $cacheKey = 'unit_kerja_tree_' . ($excludeId ?? 'all');
        
        return Cache::remember($cacheKey, 3600, function() use ($excludeId) {
            $query = UnitKerja::with('children');
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $units = $query->whereNull('report_to')->orderBy('name')->get();
            
            $tree = [];
            foreach ($units as $unit) {
                $tree[] = $this->buildNode($unit, $excludeId);
            }
            
            return $tree;
        });
    }

    /**
     * Build tree node recursively.
     */
    private function buildNode($unit, $excludeId = null)
    {
        $node = [
            'id' => $unit->id,
            'name' => $unit->name,
            'level' => $unit->level,
            'children' => []
        ];
        
        foreach ($unit->children as $child) {
            if ($excludeId && $child->id == $excludeId) {
                continue;
            }
            $node['children'][] = $this->buildNode($child, $excludeId);
        }
        
        return $node;
    }

    /**
     * Get all descendants of a unit kerja.
     */
    private function getDescendants($unitId, $includeSelf = false)
    {
        $descendants = [];
        $unit = UnitKerja::find($unitId);
        
        if ($includeSelf) {
            $descendants[] = $unit;
        }
        
        foreach ($unit->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $this->getDescendants($child->id));
        }
        
        return $descendants;
    }

    /**
     * Get level order for hierarchy validation.
     */
    private function getLevelOrder($level)
    {
        $order = [
            'Direktorat' => 1,
            'Divisi' => 2,
            'Departemen' => 3,
            'Seksi' => 4,
            'Sub Seksi' => 5
        ];
        
        return $order[$level] ?? 99;
    }

    /**
     * Check for circular reference in hierarchy.
     */
    private function isCircularReference($unitId, $newParentId)
    {
        $current = $newParentId;
        
        while ($current) {
            if ($current == $unitId) {
                return true;
            }
            $parent = UnitKerja::find($current);
            $current = $parent ? $parent->report_to : null;
        }
        
        return false;
    }

    /**
     * Get maximum hierarchy depth.
     */
    private function getMaxHierarchyDepth()
    {
        $maxDepth = 0;
        $units = UnitKerja::all();
        
        foreach ($units as $unit) {
            $depth = 0;
            $current = $unit;
            while ($current->report_to) {
                $depth++;
                $current = UnitKerja::find($current->report_to);
                if (!$current) break;
            }
            $maxDepth = max($maxDepth, $depth);
        }
        
        return $maxDepth;
    }
}