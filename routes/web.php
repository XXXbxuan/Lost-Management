<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Staff\FoundItemController;
use App\Http\Controllers\Staff\LostItemController;
use App\Http\Controllers\PickupController; // 👈 记得加这行在文件最顶端！

// 🔥🔥🔥 必须补上这一行！否则系统找不到 ClaimController 会报错 🔥🔥🔥
use App\Http\Controllers\Staff\ClaimController;

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

    // [Module 6] 保存验证结果逻辑
    Route::post('staff/match-verify/save', [\App\Http\Controllers\Staff\LostItemController::class, 'storeMatch'])
    ->name('staff.match.store');

    Route::post('staff/match/unmatch/{lostId}', [\App\Http\Controllers\Staff\LostItemController::class, 'unmatch'])
    ->name('staff.match.unmatch');
    
    // 2. 创建页
    Route::get('staff/lost-items/create', [\App\Http\Controllers\Staff\LostItemController::class, 'create'])->name('staff.lost-items.create');
    Route::post('staff/lost-items', [\App\Http\Controllers\Staff\LostItemController::class, 'store'])->name('staff.lost-items.store');

    // 🔥 [新增] 3. 匹配详情页 (这一行就是报错缺少的！)
    Route::get('staff/lost-items/{id}', [\App\Http\Controllers\Staff\LostItemController::class, 'show'])->name('staff.lost-items.show');

    Route::get('staff/match-verify/{lost_id}/{found_id}', [\App\Http\Controllers\Staff\LostItemController::class, 'verify'])
        ->name('staff.match.verify');
    
    // 修改这一行，把 'create' 改成 'createClaim'
    Route::get('staff/claims/create', [ClaimController::class, 'createClaim'])->name('staff.claims.create');

    // 这一行保持不变
    Route::post('staff/claims/store', [ClaimController::class, 'store'])->name('staff.claims.store');
    // AJAX 检查库位接口
    Route::get('staff/check-slots', [\App\Http\Controllers\Staff\FoundItemController::class, 'checkOccupiedSlots'])
    ->name('staff.check-slots');
    // 显示历史记录列表 (Audit Log)
    Route::get('staff/claims-history', [ClaimController::class, 'index'])->name('staff.claims.index');
    // 保存预约时间的路由 (就是这一行漏了！)
    Route::post('staff/claims/schedule', [ClaimController::class, 'schedule'])->name('staff.claims.schedule');
    
});
Route::get('/pickup/confirm/{token}', [PickupController::class, 'showConfirmationPage'])->name('pickup.confirm');
Route::post('/pickup/confirm/{token}', [PickupController::class, 'processConfirmation'])->name('pickup.process');
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