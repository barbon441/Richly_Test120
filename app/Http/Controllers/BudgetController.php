<?php

namespace App\Http\Controllers;

use App\Models\Budget;
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

            $budget = Budget::create([
                'user_id'      => $userId,
                'category_id'  => $validated['category_id'],
                'amount_limit' => $validated['amount_limit'],
                'amount_spent' => 0,  // เริ่มต้นที่ 0
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
}

