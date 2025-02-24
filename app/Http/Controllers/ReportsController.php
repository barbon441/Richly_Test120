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

            // ✅ กำหนดช่วงเวลาของเดือน
            $startDate = date('Y-m-01', strtotime($transactionDate));
            $endDate = date('Y-m-t', strtotime($transactionDate));

            // ✅ คำนวณรายรับและรายจ่าย
            $totalIncome = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->where('transaction_type', 'income')
                ->sum('amount');

            $totalExpense = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->where('transaction_type', 'expense')
                ->sum('amount');

            // ✅ ตรวจสอบว่ามีรายงานอยู่แล้วหรือไม่
            $report = Report::where('user_id', $userId)
                ->where('start_date', $startDate)
                ->first();

            if ($report) {
                // ✅ อัปเดตรายงานเดิม
                $report->update([
                    'total_income' => $totalIncome,
                    'total_expense' => abs($totalExpense), // ✅ บันทึกเป็นค่าบวก
                    'balance' => $totalIncome - abs($totalExpense),
                ]);
            } else {
                // ✅ สร้างรายงานใหม่
                Report::create([
                    'user_id' => $userId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_income' => $totalIncome,
                    'total_expense' => abs($totalExpense), // ✅ บันทึกเป็นค่าบวก
                    'balance' => $totalIncome - abs($totalExpense),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'รายงานอัปเดตเรียบร้อย']);
        } catch (\Exception $e) {
            Log::error("❌ เกิดข้อผิดพลาดในการอัปเดตรายงาน: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด'], 500);
        }
    }
}


