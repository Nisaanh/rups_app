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

        $keputusanQuery = Keputusan::query();
        $tindakLanjutQuery = TindakLanjut::query();
        $arahanQuery = Arahan::query();

        if (!$user->hasRole(['Admin', 'Tim Monitoring', 'Pengendali Mutu', 'Pengendali Teknis', 'Penanggung Jawab'])) {
            $tindakLanjutQuery->where('unit_kerja_id', $user->unit_kerja_id);
            $keputusanQuery->whereHas('arahan', fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id));
            $arahanQuery->where('unit_kerja_id', $user->unit_kerja_id);
        }

        $keputusanStats = $keputusanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $tindakLanjutStats = $tindakLanjutQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $arahanStats = $arahanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        // Unit Kerja Stats
        $unitKerjaStats = collect([]);
        if ($user->hasRole(['Admin', 'Tim Monitoring', 'Penanggung Jawab'])) {
            $unitKerjaStats = UnitKerja::withCount('tindakLanjut')->get()
                ->map(fn($u) => ['name' => $u->name, 'total' => $u->tindak_lanjut_count]);
        } else {
            $unitKerjaStats = UnitKerja::where('id', $user->unit_kerja_id)
                ->withCount('tindakLanjut')->get()
                ->map(fn($u) => ['name' => $u->name, 'total' => $u->tindak_lanjut_count]);
        }

        // Pending Approvals — sesuaikan relasi dengan model kamu
        $pendingApprovalsQuery = Approval::with(['tindakLanjut.unitKerja'])
            ->where('status', 'pending');

        if ($user->can('approve_stage_1')) {
            $pendingApprovalsQuery->where('stage', 1);
        } elseif ($user->can('approve_stage_2')) {
            $pendingApprovalsQuery->where('stage', 2);
        } elseif ($user->can('approve_stage_3')) {
            $pendingApprovalsQuery->where('stage', 3);
        }

        $pendingApprovals = $pendingApprovalsQuery->latest()->take(10)->get();

        return view('dashboard', [
            'keputusanStats'      => $keputusanStats,
            'tindakLanjutStats'   => $tindakLanjutStats,
            'arahanStats'         => $arahanStats,
            'unitKerjaStats'      => $unitKerjaStats,
            'is_admin'            => $user->hasRole(['Admin', 'Tim Monitoring', 'Penanggung Jawab']),
            'totalKeputusan'      => array_sum($keputusanStats),
            'totalArahan'         => array_sum($arahanStats),
            'totalArahanTerkirim' => $arahanStats['terkirim'] ?? $arahanStats['Terkirim'] ?? 0,
            'pendingApprovals'    => $pendingApprovals,
            'unitKerjaList'       => UnitKerja::orderBy('name')->get(),
        ]);
    }
}