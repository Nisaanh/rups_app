<?php

namespace App\Http\Controllers;

use App\Models\Arahan;
use App\Models\Keputusan;
use App\Models\UnitKerja;
use App\Models\User;
use App\Http\Requests\ArahanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ArahanController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Arahan::with(['keputusan', 'unitKerja', 'pic']);


        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $query->where('unit_kerja_id', $user->unit_kerja_id);
        }

        $arahan = $query->latest()->paginate(10);
        return view('arahan.index', compact('arahan'));
    }

   public function create(Request $request)
{
    if (!Gate::allows('create_arahan')) {
        abort(403);
    }

    $keputusanId = $request->get('keputusan_id');

    $keputusanSelected = null;
    if ($keputusanId) {
        $keputusanSelected = Keputusan::findOrFail($keputusanId);
    }

    $keputusan = Keputusan::whereIn('status', ['BD', 'BS'])->latest()->get();

    // Ambil unit kerja, tapi filter usernya HANYA yang memiliki role 'Auditi'
    $unitKerja = UnitKerja::with(['users' => function($query) {
        $query->role('Auditi'); // Menggunakan scope dari Spatie Permission
        // Jika tidak pakai Spatie, gunakan: 
        // $query->whereHas('roles', fn($q) => $q->where('name', 'Auditi'));
    }])->orderBy('name')->get();

    $picByUnit = [];
    foreach ($unitKerja as $unit) {
        // Mencari user pertama di unit tersebut yang rolenya Auditi
        $auditiUser = $unit->users->first();

        if ($auditiUser) {
            $picByUnit[$unit->id] = [
                'id' => $auditiUser->id,
                'name' => $auditiUser->name,
                'badge' => $auditiUser->badge ?? ''
            ];
        } else {
            // Jika di unit tersebut tidak ada Auditi, cari di unit parent
            $parentUnit = $unit->parent()->with(['users' => function($q) {
                $q->role('Auditi');
            }])->first();

            $parentAuditi = $parentUnit ? $parentUnit->users->first() : null;

            if ($parentAuditi) {
                $picByUnit[$unit->id] = [
                    'id' => $parentAuditi->id,
                    'name' => $parentAuditi->name,
                    'badge' => $parentAuditi->badge ?? ''
                ];
            }
        }
    }

    $existingArahan = collect();
    if ($keputusanId) {
        $existingArahan = Arahan::where('keputusan_id', $keputusanId)
            ->with(['unitKerja', 'pic'])
            ->latest()
            ->get();
    }

    return view('arahan.create', compact(
        'keputusan',
        'unitKerja',
        'keputusanId',
        'keputusanSelected',
        'existingArahan',
        'picByUnit'
    ));
}

    public function store(ArahanRequest $request)
    {
        if (!Gate::allows('create_arahan')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $arahan = Arahan::create($request->validated());
            DB::commit();

            // after_save dari radio button, bukan hidden input
            if ($request->after_save === 'continue') {
                return redirect()->route('arahan.create', ['keputusan_id' => $arahan->keputusan_id])
                    ->with('success', 'Butir arahan berhasil ditambahkan. Silakan tambah lagi.');
            }

            return redirect()->route('keputusan.show', $arahan->keputusan_id)
                ->with('success', 'Arahan berhasil disimpan. Klik Finalisasi jika sudah selesai.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show(Arahan $arahan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasRole('admin') && $arahan->unit_kerja_id !== $user->unit_kerja_id) {
            abort(403);
        }

        $arahan->load(['keputusan', 'unitKerja', 'pic', 'tindakLanjut']);
        return view('arahan.show', compact('arahan'));
    }

    public function edit(Arahan $arahan)
{
    if (!Gate::allows('edit_arahan')) {
        abort(403);
    }

    // Hanya bisa edit jika masih draft
    if ($arahan->status !== 'draft') {
        return redirect()->route('keputusan.show', $arahan->keputusan_id)
            ->with('error', 'Arahan yang sudah dikirim tidak dapat diubah.');
    }

    $keputusanSelected = $arahan->keputusan;
    $unitKerja = UnitKerja::with(['users'])->orderBy('name')->get();

    // Mapping PIC per unit (sama seperti create)
    $picByUnit = [];
    foreach ($unitKerja as $unit) {
        $pic = $unit->users
            ->whereIn('status', ['active'])
            ->first();
        if ($pic) {
            $picByUnit[$unit->id] = [
                'id'    => $pic->id,
                'name'  => $pic->name,
                'badge' => $pic->badge ?? ''
            ];
        }
    }

    $existingArahan = Arahan::where('keputusan_id', $arahan->keputusan_id)
        ->with(['unitKerja', 'pic'])
        ->latest()
        ->get();

    return view('arahan.edit', compact(
        'arahan',
        'keputusanSelected',
        'unitKerja',
        'picByUnit',
        'existingArahan'
    ));
}


   public function update(ArahanRequest $request, Arahan $arahan)
{
    if (!Gate::allows('edit_arahan')) {
        abort(403);
    }

    if ($arahan->status !== 'draft') {
        return redirect()->route('keputusan.show', $arahan->keputusan_id)
            ->with('error', 'Arahan yang sudah dikirim tidak dapat diubah.');
    }

    $arahan->update($request->validated());

    if ($request->after_save === 'continue') {
        return redirect()->route('arahan.create', ['keputusan_id' => $arahan->keputusan_id])
            ->with('success', 'Arahan berhasil diperbarui. Tambah arahan lagi?');
    }

    return redirect()->route('keputusan.show', $arahan->keputusan_id)
        ->with('success', 'Arahan berhasil diperbarui.');
}

    public function destroy(Arahan $arahan)
    {
        if (!Gate::allows('delete_arahan')) {
            abort(403);
        }

        $keputusanId = $arahan->keputusan_id;
        $arahan->delete();

        return redirect()->back()->with('success', 'Arahan berhasil dihapus');
    }
}
