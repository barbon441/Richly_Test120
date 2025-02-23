<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;

// ✅ ใช้ Middleware `auth:sanctum` เพื่อให้ API ใช้ได้เฉพาะ User ที่ล็อกอิน
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // ✅ API สำหรับธุรกรรม
    Route::post('/transactions', [TransactionController::class, 'store']); // เพิ่มธุรกรรม
    Route::get('/transactions', [TransactionController::class, 'index']); // ดึงธุรกรรมทั้งหมด
    Route::get('/transactions/{id}', [TransactionController::class, 'show']); // ดูรายละเอียดธุรกรรม
    Route::put('/transactions/{id}', [TransactionController::class, 'update']); // แก้ไขธุรกรรม
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']); // ลบธุรกรรม

    // ✅ API สำหรับหมวดหมู่
    Route::get('/categories', [CategoryController::class, 'index']);

    // ✅ API สำหรับงบประมาณ
    Route::post('/budgets', [BudgetController::class, 'store']);

    // ✅ API สำหรับอัปเดตรายงาน
    Route::post('/reports/update', [ReportsController::class, 'updateReport']);
});
