<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjut;
use App\Models\Arahan;
use App\Models\Approval;
use App\Models\Notification;
use App\Models\User;
use App\Http\Requests\TindakLanjutRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Arahan::with(['keputusan', 'unitKerja', 'tindakLanjut'])
            ->where('status', 'dikirim');

        if (!$user->hasRole(['Admin', 'Tim Monitoring', 'Penanggung Jawab', 'Pengendali Mutu', 'Pengendali Teknis'])) {
            $query->where('unit_kerja_id', $user->unit_kerja_id);
        }

        if ($request->filled('search')) {
            $query->where('strategi', 'like', "%{$request->search}%");
        }

        $arahan = $query->latest()->paginate(15);

        $tlQuery = TindakLanjut::query();
        if (!$user->hasRole(['Admin', 'Tim Monitoring', 'Penanggung Jawab', 'Pengendali Mutu', 'Pengendali Teknis'])) {
            $tlQuery->whereHas('arahan', fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id));
        }

        $stats = [
            'total'       => (clone $query)->count(),
            'pending'     => (clone $tlQuery)->where('status', 'pending')->count(),
            'in_approval' => (clone $tlQuery)->where('status', 'in_approval')->count(),
            'approved'    => (clone $tlQuery)->where('status', 'approved')->count(),
        ];

        return view('tindaklanjut.index', compact('arahan', 'stats'));
    }

    public function create(Request $request)
    {

        abort_if(!auth()->user()->can('create_tindak_lanjut'), 403);

        /** @var User $user */
        $user = Auth::user();

        $selectedArahanId = $request->get('arahan_id');

        $query = Arahan::where('status', 'dikirim');

        if (!$user->hasRole(['Admin', 'Tim Monitoring', 'Penanggung Jawab', 'Pengendali Mutu', 'Pengendali Teknis'])) {
            $query->where('unit_kerja_id', $user->unit_kerja_id);
        }

        $arahanList = $query->with(['keputusan', 'unitKerja'])->latest()->get();

        $historiTindakLanjut = collect();
        if ($selectedArahanId) {
            $historiTindakLanjut = TindakLanjut::where('arahan_id', $selectedArahanId)
                ->with(['creator', 'approvals'])
                ->latest()
                ->get();
        }

        if ($arahanList->isEmpty()) {
            return redirect()->route('tindaklanjut.index')
                ->with('info', 'Tidak ada arahan yang tersedia untuk unit kerja Anda.');
        }

        return view('tindaklanjut.create', compact('arahanList', 'selectedArahanId', 'historiTindakLanjut'));
    }

   public function store(TindakLanjutRequest $request)
{
    DB::beginTransaction();
    try {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['status'] = 'pending';

        if ($request->hasFile('evidence')) {
            $data['evidence_url'] = $request->file('evidence')->store('evidences', 'public');
        }

        $tindakLanjut = TindakLanjut::create($data);

        // Hanya buat stage 1 — stage berikutnya dibuat berantai saat approve
        Approval::create([
            'tindak_lanjut_id' => $tindakLanjut->id,
            'stage'            => 1,
            'stage_name'       => 'Atasan Auditi',
            'status'           => 'pending'
        ]);

        // Notifikasi ke Atasan Auditi (picUnit dari pembuat laporan)
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $atasanAuditi = $currentUser->picUnit;
        if ($atasanAuditi) {
            Notification::create([
                'user_id' => $atasanAuditi->id,
                'title'   => 'Approval Stage 1 - Atasan Auditi',
                'message' => 'Tindak lanjut baru dari unit ' . $currentUser->unitKerja->name . ' membutuhkan persetujuan Anda.',
                'type'    => 'approval',
                'data'    => ['tindak_lanjut_id' => $tindakLanjut->id, 'stage' => 1]
            ]);
        }

        DB::commit();

        if ($request->create_another === 'yes') {
            return redirect()->route('tindaklanjut.create', ['arahan_id' => $tindakLanjut->arahan_id])
                ->with('success', 'Tindak lanjut berhasil disimpan.');
        }

        return redirect()->route('tindaklanjut.index')
            ->with('success', 'Laporan berhasil dikirim ke Atasan Auditi untuk persetujuan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

    public function showArahan($id)
    {
        $arahan = Arahan::with(['keputusan', 'unitKerja', 'tindakLanjut.creator'])
            ->findOrFail($id);

        $laporanTerakhir = $arahan->tindakLanjut->sortByDesc('created_at')->first();
        $currentProgress = $laporanTerakhir ? $laporanTerakhir->progres_persen : 0;

        return view('tindaklanjut.show_arahan', compact('arahan', 'currentProgress'));
    }

    public function show(TindakLanjut $tindaklanjut)
    {
        $tindaklanjut->load(['arahan.keputusan', 'unitKerja', 'creator', 'approvals.approver']);
        return view('tindaklanjut.show', compact('tindaklanjut'));
    }

    public function edit(TindakLanjut $tindaklanjut)
    {
        if ($tindaklanjut->status !== 'pending' || $tindaklanjut->created_by !== Auth::id()) {
            return redirect()->route('tindaklanjut.index')
                ->with('error', 'Tidak dapat mengedit tindak lanjut ini.');
        }

        /** @var User $user */
        $user = Auth::user();

        $arahanList = Arahan::where('unit_kerja_id', $user->unit_kerja_id)
            ->where('status', 'dikirim')
            ->with(['keputusan', 'unitKerja'])
            ->get();

        return view('tindaklanjut.edit', compact('tindaklanjut', 'arahanList'));
    }

    public function update(TindakLanjutRequest $request, TindakLanjut $tindaklanjut)
    {
        if ($tindaklanjut->status !== 'pending' || $tindaklanjut->created_by !== Auth::id()) {
            return redirect()->route('tindaklanjut.index')
                ->with('error', 'Tidak dapat mengupdate tindak lanjut ini.');
        }

        try {
            $data = $request->validated();

            if ($request->hasFile('evidence')) {
                if ($tindaklanjut->evidence_url) {
                    Storage::disk('public')->delete($tindaklanjut->evidence_url);
                }
                $data['evidence_url'] = $request->file('evidence')->store('evidences', 'public');
            }

            $tindaklanjut->update($data);

            return redirect()->route('tindaklanjut.show', $tindaklanjut)
                ->with('success', 'Tindak lanjut berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }
    }

    public function destroy(TindakLanjut $tindaklanjut)
    {
        if ($tindaklanjut->status !== 'pending' || $tindaklanjut->created_by !== Auth::id()) {
            return redirect()->route('tindaklanjut.index')
                ->with('error', 'Tidak dapat menghapus tindak lanjut ini.');
        }

        try {
            if ($tindaklanjut->evidence_url) {
                Storage::disk('public')->delete($tindaklanjut->evidence_url);
            }

            $tindaklanjut->approvals()->delete();
            $tindaklanjut->delete();

            return redirect()->route('tindaklanjut.index')
                ->with('success', 'Tindak lanjut berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
