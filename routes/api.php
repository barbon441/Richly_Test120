<?php

use App\Http\ConttionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);


    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::post('/reports/update', [ReportsController::class, 'updateReport']);
});
