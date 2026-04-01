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
use App\Http\Controllers\Staff\InventoryController;
use App\Http\Controllers\Staff\AIChatController;
// --------------------------------------------------
// 1) 基礎重定向
Route::get('/', function () {
    return redirect()->route('login');
});

// 2) Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3) Google 登入
Route::get('auth/google', [AuthenticatedSessionController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback']);

// --------------------------------------------------
// 🌈 公開訪問路徑 (無需登入 - 供乘客確認預約)
// --------------------------------------------------
Route::prefix('pickup')->group(function () {
    Route::get('/confirm/{token}', [PickupController::class, 'showConfirmationPage'])->name('pickup.confirm');
    Route::post('/confirm/{token}', [PickupController::class, 'processConfirmation'])->name('pickup.process');

    // 🌟 QR Code 掃描後指向
    Route::get('/verify/{token}', [ClaimController::class, 'smartVerify'])->name('pickup.verify');

    Route::get('/reject/{token}', [PickupController::class, 'rejectAppointment'])->name('pickup.reject');
    Route::post('/propose/{token}', [PickupController::class, 'submitProposal'])->name('pickup.propose');
});

// --------------------------------------------------
// 🔐 認證後路徑 (需登入)
// --------------------------------------------------
Route::middleware('auth')->group(function () {

    // 個人資料
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 乘客專區 (Passenger)
    Route::prefix('passenger')->name('passenger.')->group(function () {
        Route::get('/report', [DashboardController::class, 'showReportForm'])->name('report');
        Route::get('/found-items', [DashboardController::class, 'showFoundItems'])->name('found_items');
        Route::get('/rewards', [DashboardController::class, 'showRewards'])->name('rewards');
        Route::post('/redeem/{id}', [DashboardController::class, 'redeemVoucher'])->name('redeem');
        Route::get('/history', [DashboardController::class, 'showHistory'])->name('history');
        Route::post('/voucher/{id}/use', [DashboardController::class, 'useVoucher'])->name('voucher.use');
    });

    // 員工專區 (Staff Area)
    Route::prefix('staff')->name('staff.')->group(function () {

        Route::post('/ai-chat/clear', [AIChatController::class, 'clear'])->name('ai-chat.clear');
        Route::get('/ai-chat', [AIChatController::class, 'index'])->name('ai-chat.index');
        Route::post('/ai-chat/ask', [AIChatController::class, 'ask'])->name('ai-chat.ask');
        Route::patch('/found-items/inventory/slot/{fullCode}/service', [InventoryController::class, 'markService'])->name('inventory.mark_service');
        Route::patch('/found-items/inventory/slot/{fullCode}/restore', [InventoryController::class, 'restoreSlot'])->name('inventory.restore_slot');
        Route::patch('/found-items/inventory/item/{id}/remove', [InventoryController::class, 'remove'])->name('inventory.remove');
        Route::patch('/found-items/inventory/item/{id}/move', [InventoryController::class, 'move'])->name('inventory.move');
        Route::get('/found-items/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/found-items/inventory/slot/{fullCode}', [InventoryController::class, 'showSlot'])->name('inventory.show_slot');
        // Dashboard / Export
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::get('/export/found-items', [FoundItemController::class, 'exportFoundItems'])->name('export.found_items');

        // 資源管理
        Route::resource('found-items', FoundItemController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('lost-items', LostItemController::class)->only([
            'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
        ]);

        // 匹配與驗證
        Route::get('match-verify/{lost_id}/{found_id}', [LostItemController::class, 'verify'])->name('match.verify');
        Route::post('match-verify/save', [LostItemController::class, 'storeMatch'])->name('match.store');
        Route::post('match/unmatch/{lostId}', [LostItemController::class, 'unmatch'])->name('match.unmatch');

        // Claims & Handover
        Route::prefix('claims')->name('claims.')->group(function () {
            Route::get('/', [ClaimController::class, 'index'])->name('index');
            Route::get('/create', [ClaimController::class, 'createClaim'])->name('create');
            Route::post('/store', [ClaimController::class, 'store'])->name('store');
            Route::get('/receipt/{id}', [ClaimController::class, 'showReceipt'])->name('receipt');

            Route::get('/{id}/process', [ClaimController::class, 'process'])->name('process');
            Route::get('/{id}/handover', [ClaimController::class, 'handover'])->name('handover');
            Route::post('/{id}/complete', [ClaimController::class, 'completeHandover'])->name('complete');

            // Ajax / Tools
            Route::post('/schedule', [ClaimController::class, 'schedule'])->name('schedule');
            Route::get('/check-scan', [ClaimController::class, 'checkRecentScan'])->name('check_scan');
            Route::get('/check-confirmation/{id}', [ClaimController::class, 'checkConfirmation'])->name('check_confirmation');
            Route::get('/check-reschedule/{id}', [ClaimController::class, 'checkReschedule'])->name('check_reschedule');
            Route::get('/{id}/timeline-html', [ClaimController::class, 'getTimelineHtml'])->name('timeline_html');
        });

        // Vouchers / Storage slots
        Route::resource('vouchers', VoucherController::class)->only(['index', 'store', 'destroy']);
        Route::get('check-slots', [FoundItemController::class, 'checkOccupiedSlots'])->name('check-slots');
    });
});

// 管理員專區 (Admin Only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/export/found-items', [StaffController::class, 'exportFoundItems'])->name('export.found_items');

    Route::resource('staff', StaffController::class);
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
});

// 引入基礎認證路由（login/register/logout/password.update 等都在這裡）
require __DIR__ . '/auth.php';

// OTP Reset（guest）
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [OTPPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [OTPPasswordResetController::class, 'sendResetCode'])->name('password.email');
    Route::get('reset-password-verify', [OTPPasswordResetController::class, 'showResetForm'])->name('password.verify');
    Route::post('reset-password-verify', [OTPPasswordResetController::class, 'resetPassword'])->name('password.update.otp');
});

Route::get('/test-openai', function (\App\Services\OpenAIChatService $chat) {
    return $chat->ask('What does matched status mean?');
});