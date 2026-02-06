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
// 🟢 NEW: Import the OTP Controller
use App\Http\Controllers\Auth\OTPPasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Home Page Logic
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Dashboard (Passenger / User Main Menu)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Staff Dashboard Route
Route::get('/staff/dashboard', function () {
    return view('dashboard'); 
})->middleware(['auth', 'verified'])->name('staff.dashboard');


// Google Login Routes
Route::get('auth/google', [AuthenticatedSessionController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback']);


// ====================================================
// Protected Routes (Logged In Users)
// ====================================================
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // [Passenger Side]
    Route::prefix('passenger')->name('passenger.')->group(function () {
        Route::get('/report', [DashboardController::class, 'showReportForm'])->name('report');
        Route::get('/found-items', [DashboardController::class, 'showFoundItems'])->name('found_items');
        Route::get('/rewards', [DashboardController::class, 'showRewards'])->name('rewards');
        Route::post('/redeem/{id}', [DashboardController::class, 'redeemVoucher'])->name('redeem');
        
        // My Reports History
        Route::get('/history', [DashboardController::class, 'showHistory'])->name('history');
    });

    // [Staff Side]
    
    // Found Items
    Route::get('staff/found-items', [FoundItemController::class, 'index'])->name('staff.found-items.index');
    Route::get('staff/found-items/create', [FoundItemController::class, 'create'])->name('staff.found-items.create');
    Route::post('staff/found-items', [FoundItemController::class, 'store'])->name('staff.found-items.store');

    // Lost Items
    Route::get('staff/lost-items', [LostItemController::class, 'index'])->name('staff.lost-items.index');
    Route::get('staff/lost-items/create', [LostItemController::class, 'create'])->name('staff.lost-items.create');
    Route::post('staff/lost-items', [LostItemController::class, 'store'])->name('staff.lost-items.store');
    Route::get('staff/lost-items/{id}', [LostItemController::class, 'show'])->name('staff.lost-items.show');

    // Matching & Verification
    Route::get('staff/match-verify/{lost_id}/{found_id}', [LostItemController::class, 'verify'])->name('staff.match.verify');
    Route::post('staff/match-verify/save', [LostItemController::class, 'storeMatch'])->name('staff.match.store');
    Route::post('staff/match/unmatch/{lostId}', [LostItemController::class, 'unmatch'])->name('staff.match.unmatch');
    
    // Claims
    Route::get('staff/claims/create', [ClaimController::class, 'createClaim'])->name('staff.claims.create');
    Route::post('staff/claims/store', [ClaimController::class, 'store'])->name('staff.claims.store');
    Route::get('staff/claims-history', [ClaimController::class, 'index'])->name('staff.claims.index');
    Route::post('staff/claims/schedule', [ClaimController::class, 'schedule'])->name('staff.claims.schedule');

    // Staff Voucher Management
    Route::get('staff/vouchers', [VoucherController::class, 'index'])->name('staff.vouchers.index');
    Route::post('staff/vouchers', [VoucherController::class, 'store'])->name('staff.vouchers.store');
    Route::delete('staff/vouchers/{id}', [VoucherController::class, 'destroy'])->name('staff.vouchers.destroy');

    // Helpers
    Route::get('staff/check-slots', [FoundItemController::class, 'checkOccupiedSlots'])->name('staff.check-slots');
});

// Public: Pickup Confirmation
Route::get('/pickup/confirm/{token}', [PickupController::class, 'showConfirmationPage'])->name('pickup.confirm');
Route::post('/pickup/confirm/{token}', [PickupController::class, 'processConfirmation'])->name('pickup.process');

// Admin Only Routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('staff', StaffController::class);
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    });

// Load Default Auth Routes (Login, Register, etc.)
require __DIR__.'/auth.php';

// ====================================================
// 🟢 CUSTOM OTP PASSWORD RESET ROUTES
// (Placed after auth.php to override defaults)
// ====================================================
Route::middleware('guest')->group(function () {
    // 1. Request Code Page
    Route::get('forgot-password', [OTPPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [OTPPasswordResetController::class, 'sendResetCode'])->name('password.email');

    // 2. Enter Code & Reset Page
    Route::get('reset-password-verify', [OTPPasswordResetController::class, 'showResetForm'])->name('password.verify');
    Route::post('reset-password-verify', [OTPPasswordResetController::class, 'resetPassword'])->name('password.update.otp');
});