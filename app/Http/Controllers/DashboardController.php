<?php

namespace App\Http\Controllers;

use App\Models\Keputusan;
use App\Models\TindakLanjut;
use App\Models\UnitKerja;
use App\Models\Arahan;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Tentukan siapa yang dibatasi (Hanya role Auditi yang dibatasi)
        $isAuditi = $user->hasRole('Auditi'); 

        // Query Base
        $keputusanQuery = Keputusan::query();
        $tindakLanjutQuery = TindakLanjut::query();
        $arahanQuery = Arahan::query();

        // LOGIKA FILTER: Jika dia Auditi, batasi hanya unit kerjanya saja.
        // Jika dia PJ, Teknis, Mutu, Monitoring, Admin -> Bisa lihat SEMUA (Global).
        if ($isAuditi) {
            $unitId = $user->unit_kerja_id;
            $tindakLanjutQuery->where('unit_kerja_id', $unitId);
            $arahanQuery->where('unit_kerja_id', $unitId);
            $keputusanQuery->whereHas('arahan', fn($q) => $q->where('unit_kerja_id', $unitId));
        }

        // Statistik
        $keputusanStats = $keputusanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $arahanStats = $arahanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        // Statistik Unit Kerja untuk Chart Bar
        if (!$isAuditi) {
            // PJ, Teknis, dkk melihat semua distribusi unit
            $unitKerjaStats = UnitKerja::withCount('tindakLanjut')->get()
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'total' => $u->tindak_lanjut_count]);
            $unitKerjaList = UnitKerja::orderBy('name')->get();
        } else {
            // Auditi hanya melihat bar unitnya sendiri
            $unitKerjaStats = UnitKerja::where('id', $user->unit_kerja_id)
                ->withCount('tindakLanjut')->get()
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'total' => $u->tindak_lanjut_count]);
            $unitKerjaList = collect([]); 
        }

        // Logika Pending Approvals
        $pendingApprovalsQuery = Approval::with(['tindakLanjut.unitKerja'])->where('status', 'pending');

        if ($user->can('approve_stage_1')) {
            $pendingApprovalsQuery->where('stage', 1);
        } elseif ($user->can('approve_stage_2')) {
            $pendingApprovalsQuery->where('stage', 2);
        } elseif ($user->can('approve_stage_3')) {
            $pendingApprovalsQuery->where('stage', 3);
        } else {
            $pendingApprovalsQuery->where('id', 0);
        }

        // Karena PJ & Teknis bisa approve semua unit, kita TIDAK membatasi query approval berdasarkan unit_kerja_id di sini.

        return view('dashboard', [
            'keputusanStats'      => $keputusanStats,
            'tindakLanjutStats'   => $tindakLanjutQuery->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status')->toArray(),
            'arahanStats'         => $arahanStats,
            'unitKerjaStats'      => $unitKerjaStats,
            'unitKerjaList'       => $unitKerjaList,
            'is_global'           => !$isAuditi, // True jika bukan Auditi
            'totalKeputusan'      => array_sum($keputusanStats),
            'totalArahan'         => array_sum($arahanStats),
            'totalArahanTerkirim' => $arahanStats['terkirim'] ?? $arahanStats['Terkirim'] ?? 0,
            'pendingApprovals'    => $pendingApprovalsQuery->latest()->take(10)->get(),
        ]);
    }
}