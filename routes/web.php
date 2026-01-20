<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StaffController; // ✅ 加上 Admin

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('staff/lost-items', \App\Http\Controllers\Staff\LostItemController::class)
    ->names('staff.lost-items');
});

Route::middleware(['auth', 'admin']) // 1. 必须登录 + 必须是Admin
    ->prefix('admin')                // 2. 网址前缀是 /admin/staff
    ->name('admin.')                 // 3. 路由名字前缀是 admin.staff.index
    ->group(function () {

        // 自动生成 index, create, store, edit, update, destroy 所有路由
    Route::resource('staff', StaffController::class);
    Route::get('logs', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs.index');
    
});

require __DIR__.'/auth.php';
