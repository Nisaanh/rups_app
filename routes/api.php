<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeputusanApiController;
use App\Http\Controllers\Api\TindakLanjutApiController;
use App\Http\Controllers\Api\ApprovalApiController;
use App\Http\Controllers\Api\DashboardApiController; // Tambahkan ini
use App\Http\Controllers\Api\ExportController;       // Tambahkan ini
use App\Models\UnitKerja;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Get current logged-in user profile
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'permissions');
    });

    // 2. Master Data & Transactions
    Route::apiResource('keputusan', KeputusanApiController::class);
    Route::apiResource('tindak-lanjut', TindakLanjutApiController::class);
    
    // 3. Approval System
    Route::prefix('approvals')->group(function () {
        Route::get('/pending', [ApprovalApiController::class, 'pending']);
        Route::post('/{approval}/approve', [ApprovalApiController::class, 'approve']);
        Route::post('/{approval}/reject', [ApprovalApiController::class, 'reject']);
    });

    // 4. Dashboard & Analytics
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);

    // 5. Utility: Dynamic PIC based on Unit Kerja
    // Ini dipindahkan ke dalam middleware agar aman
    Route::get('/unit-kerja/{unitKerja}/users', function($unitKerjaId) {
        $unit = UnitKerja::findOrFail($unitKerjaId);
        
        // Optimasi: Hanya ambil kolom yang diperlukan
        $users = $unit->users()
            ->where('status', 'active')
            ->select('id', 'badge', 'name', 'unit_kerja_id')
            ->get();
        
        return response()->json([
            'success' => true,
            'users' => $users->map(function($user) {
                return [
                    'id'    => $user->id,
                    'badge' => $user->badge,
                    'name'  => $user->name,
                    'role'  => $user->getRoleNames()->first() // Spatie Role
                ];
            })
        ]);
    });

    // 6. Export Features
    Route::prefix('export')->group(function () {

        Route::get('/excel', [ExportController::class, 'excel']);
    });

});