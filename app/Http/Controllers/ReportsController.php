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
            $userId = Auth::id();
            $transactionDate = $request->input('transaction_date');

            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            Log::info("📊 อัปเดตรายงานสำหรับผู้ใช้ $userId วันที่ $transactionDate");

            // ✅ ดึงช่วงวันที่ของเดือนปัจจุบัน
            $startDate = date('Y-m-01', strtotime($transactionDate));
            $endDate = date('Y-m-t', strtotime($transactionDate));

            // ✅ ดึงข้อมูลธุรกรรมของเดือนนั้น
            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->selectRaw("
                    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) as total_expense
                ")
                ->first();

            $totalIncome = $transactions->total_income ?? 0;
            $totalExpense = $transactions->total_expense ?? 0;
            $balance = $totalIncome - $totalExpense; // ✅ คำนวณคงเหลือที่ถูกต้อง

            // ✅ ใช้ updateOrCreate() เพื่อบันทึกหรืออัปเดตรายงาน
            Report::updateOrCreate(
                [
                    'user_id' => $userId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'balance' => $balance, // ✅ บันทึกค่าคงเหลือที่ถูกต้อง
                ]
            );

            Log::info("✅ รายงานอัปเดตสำเร็จ: รายรับ = $totalIncome, รายจ่าย = $totalExpense, คงเหลือ = $balance");

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตรายงานสำเร็จ!',
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


