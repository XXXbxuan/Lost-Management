<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\OTPPasswordResetController;
use App\Http\Controllers\Passenger\DashboardController;
use App\Http\Controllers\Passenger\PickupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\AIChatController;
use App\Http\Controllers\Staff\ClaimController;
use App\Http\Controllers\Staff\FoundItemController;
use App\Http\Controllers\Staff\InventoryController;
use App\Http\Controllers\Staff\LostItemController;
use App\Http\Controllers\Staff\VoucherController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('pickup')->group(function () {
    Route::get('/confirm/{token}', [PickupController::class, 'showPickupConfirmation'])->name('pickup.confirm');
    Route::post('/confirm/{token}', [PickupController::class, 'confirmPickup'])->name('pickup.process');

    Route::get('/verify/{token}', [ClaimController::class, 'verifyPickupToken'])->name('pickup.verify');

    Route::get('/reject/{token}', [PickupController::class, 'showAppointmentRejection'])->name('pickup.reject');
    Route::post('/propose/{token}', [PickupController::class, 'submitRescheduleRequest'])->name('pickup.propose');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('passenger')->name('passenger.')->group(function () {
        Route::get('/report', [DashboardController::class, 'createLostReport'])->name('report');
        Route::get('/found-items', [DashboardController::class, 'browseFoundItems'])->name('found_items');
        Route::get('/rewards', [DashboardController::class, 'showRewardsCenter'])->name('rewards');
        Route::post('/redeem/{id}', [DashboardController::class, 'redeemVoucher'])->name('redeem');
        Route::get('/history', [DashboardController::class, 'showHistory'])->name('history');
        Route::post('/voucher/{id}/use', [DashboardController::class, 'markVoucherAsUsed'])->name('voucher.use');
    });

    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'analyticsOverview'])->name('dashboard');

        Route::get('/ai-chat', [AIChatController::class, 'index'])->name('ai-chat.index');
        Route::post('/ai-chat/ask', [AIChatController::class, 'sendMessage'])->name('ai-chat.ask');
        Route::post('/ai-chat/clear', [AIChatController::class, 'clearHistory'])->name('ai-chat.clear');

        Route::get('/found-items/inventory', [InventoryController::class, 'showInventoryMap'])->name('inventory.index');
        Route::get('/found-items/inventory/slot/{fullCode}', [InventoryController::class, 'showSlotDetails'])->name('inventory.show_slot');
        Route::patch('/found-items/inventory/slot/{fullCode}/service', [InventoryController::class, 'markSlotAsService'])->name('inventory.mark_service');
        Route::patch('/found-items/inventory/slot/{fullCode}/restore', [InventoryController::class, 'restoreServiceSlot'])->name('inventory.restore_slot');
        Route::patch('/found-items/inventory/item/{id}/remove', [InventoryController::class, 'removeFoundItem'])->name('inventory.remove');
        Route::patch('/found-items/inventory/item/{id}/move', [InventoryController::class, 'moveFoundItem'])->name('inventory.move');

        Route::resource('found-items', FoundItemController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::get('check-slots', [FoundItemController::class, 'getOccupiedSlots'])->name('check-slots');

        Route::resource('lost-items', LostItemController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::get('match-verify/{lost_id}/{found_id}', [LostItemController::class, 'showMatchVerification'])->name('match.verify');
        Route::post('match-verify/save', [LostItemController::class, 'storeMatchVerification'])->name('match.store');
        Route::post('match/unmatch/{lostId}', [LostItemController::class, 'undoMatch'])->name('match.unmatch');

        Route::prefix('claims')->name('claims.')->group(function () {
            Route::get('/', [ClaimController::class, 'index'])->name('index');
            Route::get('/receipt/{id}', [ClaimController::class, 'showClaimReceipt'])->name('receipt');

            Route::get('/{id}/process', [ClaimController::class, 'showClaimProcessing'])->name('process');
            Route::get('/{id}/handover', [ClaimController::class, 'showHandoverWorkflow'])->name('handover');
            Route::post('/{id}/complete', [ClaimController::class, 'finalizeHandover'])->name('complete');

            Route::post('/schedule', [ClaimController::class, 'scheduleAppointment'])->name('schedule');
            Route::get('/check-scan', [ClaimController::class, 'checkRecentScan'])->name('check_scan');
            Route::get('/check-confirmation/{id}', [ClaimController::class, 'checkConfirmationStatus'])->name('check_confirmation');
            Route::get('/check-reschedule/{id}', [ClaimController::class, 'checkRescheduleStatus'])->name('check_reschedule');
            Route::get('/{id}/timeline-html', [ClaimController::class, 'renderTimelineHtml'])->name('timeline_html');
        });

        Route::resource('vouchers', VoucherController::class)
            ->only(['index', 'store', 'destroy']);
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'analyticsOverview'])->name('dashboard');
    Route::get('/export/found-items', [FoundItemController::class, 'exportFoundItemsReport'])->name('export.found_items');

    Route::resource('staff', StaffController::class);
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

require __DIR__ . '/auth.php';

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [OTPPasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('forgot-password', [OTPPasswordResetController::class, 'sendResetCode'])->name('password.email');
    Route::get('reset-password-verify', [OTPPasswordResetController::class, 'showVerificationForm'])->name('password.verify');
    Route::post('reset-password-verify', [OTPPasswordResetController::class, 'resetPassword'])->name('password.update.otp');
});