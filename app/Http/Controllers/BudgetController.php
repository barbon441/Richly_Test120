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
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // ✅ ดึงข้อมูลหมวดหมู่จากตาราง categories
            $budgets = Budget::where('user_id', $userId)
                ->join('categories', 'budgets.category_id', '=', 'categories.id')
                ->select('budgets.*', 'categories.name as category_name') // ✅ เพิ่มชื่อหมวดหมู่
                ->get();

            return response()->json([
                'success' => true,
                'budgets' => $budgets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ ดึงข้อมูลงบประมาณเดิม
    public function show($id)
    {
        try {
            $budget = Budget::where('id', $id)
                            ->where('user_id', Auth::id()) // ✅ ให้ดึงเฉพาะของ user นี้
                            ->first();

            if (!$budget) {
                return response()->json(['success' => false, 'message' => 'ไม่พบนงบประมาณ'], 404);
            }

            return response()->json(['success' => true, 'budget' => $budget]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ อัปเดตงบประมาณ
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'category_id'  => 'required|integer|exists:categories,id',
                'amount_limit' => 'required|numeric|min:0',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date|after_or_equal:start_date',
            ]);

            $budget = Budget::where('id', $id)
                            ->where('user_id', Auth::id()) // ✅ ให้แก้ไขเฉพาะของ user นี้
                            ->first();

            if (!$budget) {
                return response()->json(['success' => false, 'message' => 'ไม่พบนงบประมาณ'], 404);
            }

            $budget->update([
                'category_id'  => $validated['category_id'],
                'amount_limit' => $validated['amount_limit'],
                'start_date'   => $validated['start_date'],
                'end_date'     => $validated['end_date'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตงบประมาณสำเร็จ!',
                'budget'  => $budget
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ ลบงบประมาณ
    public function destroy($id)
{
    try {
        $budget = Budget::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$budget) {
            return response()->json(['success' => false, 'message' => 'ไม่พบนงบประมาณ'], 404);
        }

        $budget->delete();

        return response()->json(['success' => true, 'message' => 'ลบงบประมาณสำเร็จ!']);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        ], 500);
    }
}


}
