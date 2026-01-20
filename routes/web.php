<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\LogController;
// [Module 4] Found Items
use App\Http\Controllers\Staff\FoundItemController;
// [Module 5] Lost Items (配合你的 Diagram 名字)
use App\Http\Controllers\Staff\LostItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ====================================================
// 普通登录用户 (Staff & Admin) 都可以访问的路由
// ====================================================
Route::middleware('auth')->group(function () {
    // 个人资料 (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // [Module 4] Found Items (拾获物品管理 - Staff Side)
    // 对应: FoundItemController
    Route::get('staff/found-items', [FoundItemController::class, 'index'])->name('staff.found-items.index');
    Route::get('staff/found-items/create', [FoundItemController::class, 'create'])->name('staff.found-items.create');
    Route::post('staff/found-items', [FoundItemController::class, 'store'])->name('staff.found-items.store');

    // [Module 5] Passenger Lost Items (乘客报失单 - Staff Side)
    // 对应: LostItemController (为了匹配 Diagram)
    Route::get('staff/lost-items', [\App\Http\Controllers\Staff\LostItemController::class, 'index'])->name('staff.lost-items.index');
    
    // 2. 创建页 (Create)
    Route::get('staff/lost-items/create', [\App\Http\Controllers\Staff\LostItemController::class, 'create'])->name('staff.lost-items.create');
    
    // 3. 保存逻辑 (Store)
    Route::post('staff/lost-items', [\App\Http\Controllers\Staff\LostItemController::class, 'store'])->name('staff.lost-items.store');
});

// ====================================================
// 只有管理员 (Admin) 可以访问的路由
// ====================================================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 员工账号管理 (Staff Management)
        Route::resource('staff', StaffController::class);

        // 审计日志 (Audit Logs)
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
        
    });

require __DIR__.'/auth.php';