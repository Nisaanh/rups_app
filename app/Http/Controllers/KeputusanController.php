<?php

namespace App\Http\Controllers;

use App\Models\Keputusan;
use App\Models\Notification;
use App\Http\Requests\KeputusanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class KeputusanController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Keputusan::with('creator');

        // Cek apakah user adalah Auditi atau Atasan Auditi
        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            // Hanya lihat keputusan yang memiliki arahan untuk unit kerjanya
            $query->whereHas('arahan', function ($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }
        $keputusan = $query->latest()->paginate(10);
        return view('keputusan.index', compact('keputusan'));
    }

    public function create()
    {
        if (!Gate::allows('create_keputusan')) {
            abort(403);
        }

        $tahunSekarang = date('Y');
        // Gunakan ID terakhir untuk auto-numbering yang lebih aman
        $lastId = Keputusan::max('id') ?? 0;
        $nextNumber = $lastId + 1;

        $autoNumber = "KEP-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . "/RUPS/" . $tahunSekarang;

        return view('keputusan.create', compact('autoNumber'));
    }

    public function store(KeputusanRequest $request)
    {
        if (!Gate::allows('create_keputusan')) {
            abort(403);
        }

        $keputusan = Keputusan::create([
            'nomor_keputusan' => $request->nomor_keputusan,
            'periode_year'    => $request->periode_year,
            'status'          => 'BD', // Belum Dikirim (Draft)
            'created_by'      => Auth::id()
        ]);

        return redirect()->route('arahan.create', ['keputusan_id' => $keputusan->id])
            ->with('success', 'Keputusan berhasil dibuat. Silakan tambahkan butir-butir arahan.');
    }



    /**
     * Method Baru: Finalisasi untuk mengirim semua arahan sekaligus
     */
    public function finalize(Keputusan $keputusan)
    {
        if (!Gate::allows('create_keputusan')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // 1. Update status keputusan
            $keputusan->update(['status' => 'BS']);

            // 2. Update status ARAHAN di database secara langsung (Gunakan string 'dikirim')
            // Pastikan nama kolomnya benar 'status'
            DB::table('arahan')->where('keputusan_id', $keputusan->id)->update(['status' => 'dikirim']);

            // 3. Ambil data segar dari database agar notifikasi benar
            $arahanList = $keputusan->arahan()->get();

            foreach ($arahanList as $arahan) {
                Notification::create([
                    'user_id' => $arahan->pic_unit_kerja_id,
                    'title'   => 'Arahan RUPS Baru',
                    'message' => "Anda menerima arahan baru dari Keputusan No: {$keputusan->nomor_keputusan}",
                    'type'    => 'arahan',
                    'data'    => json_encode(['arahan_id' => $arahan->id])
                ]);
            }

            DB::commit();

            // Gunakan refresh() agar model keputusan membawa data terbaru ke view
            return redirect()->route('keputusan.show', $keputusan->refresh())
                ->with('success', 'Semua arahan telah difinalisasi.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show(Keputusan $keputusan)
    {
        $keputusan->load(['arahan.unitKerja', 'arahan.pic', 'creator']);
        return view('keputusan.show', compact('keputusan'));
    }

    public function edit(Keputusan $keputusan)
    {
        // Cek permission sesuai role spa kamu
        if (!Gate::allows('edit_keputusan')) {
            abort(403);
        }

        // ALUR LOGIKA BARU:
        // Jika status masih draft (Belum Dikirim), arahkan balik ke form buat arahan
        if ($keputusan->status === 'BD') {
            return redirect()->route('arahan.create', ['keputusan_id' => $keputusan->id])
                ->with('info', 'Silakan lanjutkan pengisian butir arahan.');
        }

        // Jika sudah dikirim, baru tampilkan view edit biasa (misal ganti nomor/tahun)
        return view('keputusan.edit', compact('keputusan'));
    }

    public function update(Request $request, Keputusan $keputusan)
    {
        $request->validate([
            'periode_year' => 'required|numeric',
            'nomor_keputusan' => 'required|string|max:255',
        ]);

        $keputusan->update($request->all());

        return redirect()->route('keputusan.index')
            ->with('success', 'Data Keputusan berhasil diperbarui.');
    }

    public function destroy(Keputusan $keputusan)
    {
        if (!Gate::allows('delete_keputusan')) {
            abort(403);
        }
        $keputusan->delete();
        return redirect()->route('keputusan.index')->with('success', 'Keputusan berhasil dihapus');
    }
}
