<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class BudgetController extends Controller
{
    // ✅ เพิ่มงบประมาณใหม่
    public function store(Request $request)
    {
        try {
            Log::info("📥 Data received in Budget:", $request->all());

            $validated = $request->validate([
                'category_id'  => 'required|exists:categories,id',
                'amount_limit' => 'required|numeric|min:0',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date|after_or_equal:start_date',
            ]);

            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // ✅ ตรวจสอบว่ามีงบประมาณสำหรับช่วงเวลานี้อยู่แล้วหรือไม่
            $existingBudget = Budget::where('category_id', $validated['category_id'])
                ->where('user_id', $userId)
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                          ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                          ->orWhere(function ($q) use ($validated) {
                              $q->where('start_date', '<=', $validated['start_date'])
                                ->where('end_date', '>=', $validated['end_date']);
                          });
                })
                ->exists();

            if ($existingBudget) {
                return response()->json([
                    'success' => false,
                    'message' => 'มีงบประมาณสำหรับหมวดหมู่นี้ในช่วงเวลานี้อยู่แล้ว'
                ], 400);
            }

            // ✅ คำนวณค่าใช้จ่าย (amount_spent) เฉพาะธุรกรรมที่เกี่ยวข้อง
            $amountSpent = Transaction::where('category_id', $validated['category_id'])
                ->where('user_id', $userId)
                ->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']])
                ->sum('amount');

            // ✅ สร้างงบประมาณใหม่
            $budget = Budget::create([
                'user_id'      => $userId,
                'category_id'  => $validated['category_id'],
                'amount_limit' => $validated['amount_limit'],
                'amount_spent' => $amountSpent, // ✅ อัปเดตค่าที่ถูกต้อง
                'start_date'   => $validated['start_date'],
                'end_date'     => $validated['end_date'],
            ]);

            Log::info("✅ บันทึกงบประมาณสำเร็จ:", $budget->toArray());

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มงบประมาณสำเร็จ!',
                'budget'  => $budget
            ], 201);

        } catch (\Exception $e) {
            Log::error("❌ Error ในการบันทึกงบประมาณ: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ ฟังก์ชันอัปเดตงบประมาณอัตโนมัติ
    public function updateBudget($transaction)
    {
        try {
            $budget = Budget::where('category_id', $transaction->category_id)
                ->where('user_id', $transaction->user_id)
                ->where('start_date', '<=', $transaction->transaction_date)
                ->where('end_date', '>=', $transaction->transaction_date)
                ->first();

            if ($budget) {
                // ✅ คำนวณค่าใช้จ่ายใหม่
                $budget->amount_spent = Transaction::where('category_id', $budget->category_id)
                    ->where('user_id', $budget->user_id)
                    ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
                    ->sum('amount');

                $budget->save();
                Log::info("✅ งบประมาณอัปเดตสำเร็จ", $budget->toArray());
            }

        } catch (\Exception $e) {
            Log::error("❌ Error ในการอัปเดตงบประมาณ: " . $e->getMessage());
        }
    }
}
