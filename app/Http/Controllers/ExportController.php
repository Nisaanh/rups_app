<?php

namespace App\Http\Controllers;

use App\Models\Arahan;
use App\Models\Keputusan;
use App\Models\TindakLanjut;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    // ExportController@index — ubah jadi load data juga
public function index(Request $request)
{
    /** @var \App\Models\User $user */
    $user    = Auth::user();
    $isAdmin = $user->hasRole([
        'Admin', 'Tim Monitoring', 'Penanggung Jawab',
        'Pengendali Mutu', 'Pengendali Teknis'
    ]);

    $unitKerjaList = collect();
    if ($isAdmin) {
        $unitKerjaList = UnitKerja::orderBy('name')->get();
    } elseif ($user->hasRole('Atasan Auditi')) {
        $unitKerjaList = UnitKerja::whereIn('id',
            $user->subordinates->pluck('unit_kerja_id')->unique()
        )->get();
    }

    $keputusanList = Keputusan::orderByDesc('periode_year')->get();
    if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
        $keputusanList = Keputusan::whereHas('arahan',
            fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id)
        )->orderByDesc('periode_year')->get();
    }

    // Query data tabel — sama dengan download tapi paginate
    $query = TindakLanjut::with([
        'arahan.keputusan', 'arahan.unitKerja',
        'unitKerja', 'creator', 'approvals',
    ]);

    if ($isAdmin) {
        if ($request->filled('unit_kerja_id')) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }
    } elseif ($user->hasRole('Atasan Auditi')) {
        $subordinateUnitIds = $user->subordinates->pluck('unit_kerja_id')->unique();
        $query->whereIn('unit_kerja_id', $subordinateUnitIds);
        if ($request->filled('unit_kerja_id')) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }
    } else {
        $query->where('unit_kerja_id', $user->unit_kerja_id)
              ->where('created_by', $user->id);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('keputusan_id')) {
        $query->whereHas('arahan', fn($q) => $q->where('keputusan_id', $request->keputusan_id));
    }
    if ($request->filled('periode_bulan')) {
        $query->where('periode_bulan', $request->periode_bulan);
    }
    if ($request->filled('periode_tahun')) {
        $query->where('periode_tahun', $request->periode_tahun);
    }

    $data  = $query->latest()->paginate(15)->withQueryString();
    $total = $query->count();

    return view('export.index', compact(
        'isAdmin', 'unitKerjaList', 'keputusanList',
        'data', 'total'
    ));
}

    public function download(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $isAdmin = $user->hasRole([
            'Admin', 'Tim Monitoring', 'Penanggung Jawab',
            'Pengendali Mutu', 'Pengendali Teknis'
        ]);

        $jenis = $request->get('jenis', 'tindaklanjut');

        if ($jenis === 'keputusan') {
            return $this->exportKeputusan($request, $user, $isAdmin);
        }

        return $this->exportTindakLanjut($request, $user, $isAdmin);
    }

    private function exportTindakLanjut($request, $user, $isAdmin)
    {
        $query = TindakLanjut::with([
            'arahan.keputusan',
            'arahan.unitKerja',
            'unitKerja',
            'creator',
            'approvals.approver',
        ]);

        // Filter role
        if ($isAdmin) {
            if ($request->filled('unit_kerja_id')) {
                $query->where('unit_kerja_id', $request->unit_kerja_id);
            }
        } elseif ($user->hasRole('Atasan Auditi')) {
            $subordinateUnitIds = $user->subordinates->pluck('unit_kerja_id')->unique();
            $query->whereIn('unit_kerja_id', $subordinateUnitIds);
            if ($request->filled('unit_kerja_id')) {
                $query->where('unit_kerja_id', $request->unit_kerja_id);
            }
        } else {
            // Auditi — hanya milik sendiri
            $query->where('unit_kerja_id', $user->unit_kerja_id)
                  ->where('created_by', $user->id);
        }

        // Filter tambahan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keputusan_id')) {
            $query->whereHas('arahan', fn($q) => $q->where('keputusan_id', $request->keputusan_id));
        }

        if ($request->filled('periode_bulan')) {
            $query->where('periode_bulan', $request->periode_bulan);
        }

        if ($request->filled('periode_tahun')) {
            $query->where('periode_tahun', $request->periode_tahun);
        }

        $data     = $query->latest()->get();
        $roleName = $user->getRoleNames()->first();
        $filename = 'tindaklanjut_' . str_replace(' ', '_', $roleName) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($data, $isAdmin, $user) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Kolom header sesuai role
            if ($isAdmin) {
                fputcsv($file, [
                    'No', 'Nomor Keputusan', 'Periode Keputusan',
                    'Unit Kerja', 'Arahan/Strategi', 'Bulan/Tahun Laporan',
                    'Uraian Tindak Lanjut', 'Kendala', 'Status',
                    'Progress Stage', 'Dibuat Oleh', 'Tanggal Input',
                    'Catatan Approver Terakhir',
                ]);
            } elseif ($user->hasRole('Atasan Auditi')) {
                fputcsv($file, [
                    'No', 'Nomor Keputusan', 'Unit Kerja',
                    'Arahan/Strategi', 'Bulan/Tahun Laporan',
                    'Uraian Tindak Lanjut', 'Kendala',
                    'Status', 'Dibuat Oleh', 'Tanggal Input',
                ]);
            } else {
                fputcsv($file, [
                    'No', 'Nomor Keputusan', 'Arahan/Strategi',
                    'Bulan/Tahun Laporan', 'Uraian Tindak Lanjut',
                    'Kendala', 'Status', 'Tanggal Input',
                ]);
            }

            $statusLabel = [
                'pending'     => 'Menunggu Verifikasi',
                'in_approval' => 'Sedang Diverifikasi',
                'approved'    => 'Selesai',
                'rejected'    => 'Revisi / TD',
            ];

            foreach ($data as $i => $tl) {
                $approvedStages  = $tl->approvals->where('status', 'approved')->count();
                $lastNote        = $tl->approvals->whereNotNull('note')->sortByDesc('updated_at')->first();
                $status          = $statusLabel[$tl->status] ?? $tl->status;
                $periodelaporan  = $tl->periode_bulan . '/' . $tl->periode_tahun;
                $nomorKeputusan  = $tl->arahan->keputusan->nomor_keputusan ?? '-';
                $strategi        = $tl->arahan->strategi ?? '-';
                $unitKerja       = $tl->unitKerja->name ?? '-';
                $periode         = $tl->arahan->keputusan->periode_year ?? '-';
                $createdBy       = $tl->creator->name ?? '-';
                $tanggalInput    = $tl->created_at->format('d/m/Y H:i');
                $catatanApprover = $lastNote->note ?? '-';

                if ($isAdmin) {
                    fputcsv($file, [
                        $i + 1, $nomorKeputusan, $periode,
                        $unitKerja, $strategi, $periodelaporan,
                        $tl->tindak_lanjut, $tl->kendala ?? '-',
                        $status, "Stage {$approvedStages}/5",
                        $createdBy, $tanggalInput, $catatanApprover,
                    ]);
                } elseif ($user->hasRole('Atasan Auditi')) {
                    fputcsv($file, [
                        $i + 1, $nomorKeputusan, $unitKerja,
                        $strategi, $periodelaporan,
                        $tl->tindak_lanjut, $tl->kendala ?? '-',
                        $status, $createdBy, $tanggalInput,
                    ]);
                } else {
                    fputcsv($file, [
                        $i + 1, $nomorKeputusan, $strategi,
                        $periodelaporan, $tl->tindak_lanjut,
                        $tl->kendala ?? '-', $status, $tanggalInput,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportKeputusan($request, $user, $isAdmin)
    {
        $query = Keputusan::with(['creator', 'arahan.unitKerja']);

        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $query->whereHas('arahan', fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('periode_tahun')) {
            $query->where('periode_year', $request->periode_tahun);
        }

        $data     = $query->latest()->get();
        $filename = 'keputusan_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No', 'Nomor Keputusan', 'Periode',
                'Status', 'Jumlah Arahan', 'Dibuat Oleh', 'Tanggal Dibuat',
            ]);

            $statusLabel = [
                'BD' => 'Belum Dikirim',
                'BS' => 'Belum Selesai',
                'S'  => 'Selesai',
                'TD' => 'Tidak Dapat Ditindaklanjuti',
            ];

            foreach ($data as $i => $kep) {
                fputcsv($file, [
                    $i + 1,
                    $kep->nomor_keputusan,
                    $kep->periode_year,
                    $statusLabel[$kep->status] ?? $kep->status,
                    $kep->arahan->count(),
                    $kep->creator->name ?? '-',
                    $kep->created_at->format('d/m/Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}