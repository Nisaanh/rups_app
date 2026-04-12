<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeputusanController;
use App\Http\Controllers\ArahanController;
use App\Http\Controllers\TindakLanjutController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
    Route::get('/dashboard/approval-stats', [DashboardController::class, 'approvalStats'])->name('dashboard.approval-stats');

    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Role Management ---
    Route::middleware('permission:manage_roles')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/permissions', [RoleController::class, 'permissions'])->name('permissions');
        Route::post('/permissions', [RoleController::class, 'createPermission'])->name('permissions.store');
        Route::get('/user-assignments', [RoleController::class, 'userAssignments'])->name('user-assignments');
        Route::post('/bulk-assign', [RoleController::class, 'bulkAssign'])->name('bulk-assign');
        Route::get('/statistics', [RoleController::class, 'statistics'])->name('statistics');
        Route::get('/export', [RoleController::class, 'export'])->name('export');
        Route::post('/refresh-cache', [RoleController::class, 'refreshCache'])->name('refresh-cache');
        
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // --- Unit Kerja Management ---
    Route::middleware('permission:manage_unit_kerja')->prefix('unit-kerja')->name('unit-kerja.')->group(function () {
        Route::get('/', [UnitKerjaController::class, 'index'])->name('index');
        Route::get('/create', [UnitKerjaController::class, 'create'])->name('create');
        Route::post('/', [UnitKerjaController::class, 'store'])->name('store');
        Route::get('/statistics', [UnitKerjaController::class, 'statistics'])->name('statistics');
        Route::get('/search', [UnitKerjaController::class, 'search'])->name('search');
        Route::post('/import', [UnitKerjaController::class, 'import'])->name('import');
        Route::get('/export', [UnitKerjaController::class, 'export'])->name('export');
        
        Route::get('/{unitKerja}', [UnitKerjaController::class, 'show'])->name('show');
        Route::get('/{unitKerja}/edit', [UnitKerjaController::class, 'edit'])->name('edit');
        Route::put('/{unitKerja}', [UnitKerjaController::class, 'update'])->name('update');
        Route::delete('/{unitKerja}', [UnitKerjaController::class, 'destroy'])->name('destroy');
        Route::get('/{unitKerja}/users', [UnitKerjaController::class, 'getUsers'])->name('get-users');
    });

    // --- User Management ---
    Route::middleware('permission:manage_users')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        
        // My Profile (Accessible by anyone auth)
        Route::get('/my-profile', [UserController::class, 'profile'])->name('profile')->withoutMiddleware('permission:manage_users');
        Route::put('/my-profile/update', [UserController::class, 'updateProfile'])->name('update-profile')->withoutMiddleware('permission:manage_users');
        
        Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // --- Business Logic ---
    Route::resource('keputusan', KeputusanController::class);
    Route::post('/keputusan/{keputusan}/finalize', [KeputusanController::class, 'finalize'])
    ->name('keputusan.finalize');
    Route::resource('arahan', ArahanController::class);
    Route::resource('tindaklanjut', TindakLanjutController::class);
    Route::get('/tindaklanjut/arahan/{id}', [TindakLanjutController::class, 'showArahan'])->name('tindaklanjut.show_arahan');
    
    // --- Approval System ---
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/{tindaklanjut}', [ApprovalController::class, 'show'])->name('show'); // Tambahkan ini
        Route::post('/{tindaklanjut}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{tindaklanjut}/reject', [ApprovalController::class, 'reject'])->name('reject');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
