<?php

use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;



// ✅ หน้า Home
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// ✅ ใช้ Middleware `auth` สำหรับหน้าเว็บที่ต้อง Login เท่านั้น
Route::middleware(['auth'])->group(function () {

    // ✅ หน้า Dashboard
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // ✅ หน้าเพิ่มธุรกรรม (แสดงหน้า AddTransaction)
    Route::get('/transactions/add', fn () => Inertia::render('AddTransaction'))->name('transactions.add');

    // ✅ หน้าแก้ไข Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
});

// ✅ ต้องใช้ `auth.php` สำหรับ Authentication Routes (Register, Forgot Password, Reset Password)
require __DIR__.'/auth.php';
