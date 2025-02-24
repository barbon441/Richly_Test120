<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ReportsController extends Controller
{
        public function updateReport(Request $request)
    {
        try {
            $startTime = microtime(true); // ⏳ จับเวลาเริ่มต้น

            $userId = Auth::id();
            $transactionDate = $request->input('transaction_date');

            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            Log::info("📊 เริ่มอัปเดตรายงานสำหรับผู้ใช้ $userId วันที่ $transactionDate");

            // ✅ ดึงช่วงวันที่ของเดือน
            $startDate = date('Y-m-01', strtotime($transactionDate));
            $endDate = date('Y-m-t', strtotime($transactionDate));

            // ✅ คำนวณโดยใช้ Query เดียว ลดการโหลดข้อมูล
            $transactions = DB::table('transactions')
                ->where('user_id', $userId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                    COALESCE(SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
                ")
                ->first();

            $totalIncome = $transactions->total_income;
            $totalExpense = $transactions->total_expense;
            $balance = $totalIncome - $totalExpense;

            // ✅ บันทึกหรืออัปเดตรายงาน
            Report::updateOrCreate(
                [
                    'user_id' => $userId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'balance' => $balance,
                ]
            );

            $executionTime = round(microtime(true) - $startTime, 2); // ⏳ คำนวณเวลาที่ใช้
            Log::info("✅ อัปเดตรายงานสำเร็จใน {$executionTime} วินาที");

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตรายงานสำเร็จ!',
                'execution_time' => $executionTime
            ], 200);

        } catch (\Exception $e) {
            Log::error("❌ Error ในการอัปเดตรายงาน: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }
}


