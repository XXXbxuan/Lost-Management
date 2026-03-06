<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Staff\FoundItemController;
use App\Http\Controllers\Staff\LostItemController;
use App\Http\Controllers\Staff\ClaimController;
use App\Http\Controllers\Staff\VoucherController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\Passenger\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OTPPasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes - Secure Lost & Found System
|--------------------------------------------------------------------------
*/

// --- 1. 基礎入口 ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/staff/dashboard', function () {
    return view('dashboard'); 
})->middleware(['auth', 'verified'])->name('staff.dashboard');

// --- 2. Google 第三方登入 ---
Route::get('auth/google', [AuthenticatedSessionController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback']);

// ====================================================
// 🌈 3. 公開訪問路徑 (無需登入)
// ====================================================
Route::prefix('pickup')->group(function () {
    Route::get('/confirm/{token}', [PickupController::class, 'showConfirmationPage'])->name('pickup.confirm');
    Route::post('/confirm/{token}', [PickupController::class, 'processConfirmation'])->name('pickup.process');

    // 🌟 核心：智能分流入口 (QR Code 掃描後指向此處)
    Route::get('/verify/{token}', [ClaimController::class, 'smartVerify'])->name('pickup.verify');

    Route::get('/reject/{token}', [PickupController::class, 'rejectAppointment'])->name('pickup.reject');
    Route::post('/propose/{token}', [PickupController::class, 'submitProposal'])->name('pickup.propose');
});

// ====================================================
// 🔐 4. 登入保護路由 (Logged In Users Only)
// ====================================================
Route::middleware('auth')->group(function () {
    
    // 個人資料管理
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- [Passenger 旅客端功能群組] ---
    Route::prefix('passenger')->name('passenger.')->group(function () {
        Route::get('/report', [DashboardController::class, 'showReportForm'])->name('report');
        Route::get('/found-items', [DashboardController::class, 'showFoundItems'])->name('found_items');
        Route::get('/rewards', [DashboardController::class, 'showRewards'])->name('rewards');
        Route::post('/redeem/{id}', [DashboardController::class, 'redeemVoucher'])->name('redeem');
        Route::get('/history', [DashboardController::class, 'showHistory'])->name('history');
    });

    // --- [Staff 工作人員端功能群組] ---
    Route::prefix('staff')->name('staff.')->group(function () {
        
        // 資源管理
        Route::resource('found-items', FoundItemController::class)->only(['index', 'create', 'store']);
        Route::resource('lost-items', LostItemController::class)->only(['index', 'create', 'store', 'show']);

        // 匹配與驗證邏輯
        Route::get('match-verify/{lost_id}/{found_id}', [LostItemController::class, 'verify'])->name('match.verify');
        Route::post('match-verify/save', [LostItemController::class, 'storeMatch'])->name('match.store');
        Route::post('match/unmatch/{lostId}', [LostItemController::class, 'unmatch'])->name('match.unmatch');
        
        // 🌟 領取管理 (Claims & Handover)
        // 這裡的層級會產生：staff.claims.xxx 的路由名稱
        Route::prefix('claims')->name('claims.')->group(function () {
            
            // 📜 結案歷史紀錄 -> route('staff.claims.history')
            Route::get('/', [ClaimController::class, 'index'])->name('index'); 

            // 🧾 正式收據 -> route('staff.claims.receipt')
            Route::get('/receipt/{id}', [ClaimController::class, 'showReceipt'])->name('receipt');

            // 🧪 現場處理流程
            Route::get('/{id}/process', [ClaimController::class, 'process'])->name('process');
            Route::get('/{id}/handover', [ClaimController::class, 'handover'])->name('handover');
            Route::post('/{id}/complete', [ClaimController::class, 'completeHandover'])->name('complete');

            // 📡 工具類 (Ajax/Logic)
            Route::post('/schedule', [ClaimController::class, 'schedule'])->name('schedule');
            Route::get('/check-scan', [ClaimController::class, 'checkRecentScan'])->name('check_scan');
            Route::get('/check-confirmation/{id}', [ClaimController::class, 'checkConfirmation'])->name('check_confirmation');
            Route::get('/{id}/timeline-html', [ClaimController::class, 'getTimelineHtml'])->name('timeline_html');
        });

        // 其他功能
        Route::resource('vouchers', VoucherController::class)->only(['index', 'store', 'destroy']);
        Route::get('check-slots', [FoundItemController::class, 'checkOccupiedSlots'])->name('check-slots');
    });
});

// --- 5. 管理員專屬 (Admin Only) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('staff', StaffController::class);
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
});

// --- 6. 身份驗證與密碼重設 ---
require __DIR__.'/auth.php';

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [OTPPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [OTPPasswordResetController::class, 'sendResetCode'])->name('password.email');
    Route::get('reset-password-verify', [OTPPasswordResetController::class, 'showResetForm'])->name('password.verify');
    Route::post('reset-password-verify', [OTPPasswordResetController::class, 'resetPassword'])->name('password.update.otp');
});