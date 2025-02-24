<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class BudgetController extends Controller
{
    // ดึงข้อมูลงบประมาณทั้งหมด
    public function index() {
        return Inertia::render('AddBudget');
    }
    // บันทึกงบประมาณใหม่
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount_limit' => 'required|numeric|min:0',
            'start_date' => 'required|date',  // ✅ ตรวจสอบวันที่เริ่มต้น
            'end_date' => 'required|date|after_or_equal:start_date',  // ✅ ตรวจสอบวันที่สิ้นสุด
        ]);

         // ✅ เพิ่มข้อมูลงบประมาณ
        $budget = Budget::create([
            'user_id' => auth()->id(), // ✅ ดึง user_id จาก auth
            'category_id' => $validated['category_id'],
            'amount_limit' => $validated['amount_limit'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return response()->json(['message' => 'บันทึกงบประมาณสำเร็จ!', 'budget' => $budget], 201);
    }

    // ดึงข้อมูลงบประมาณที่เลือก
    public function show($id)
    {
        return response()->json(Budget::findOrFail($id), 200);
    }

    // อัปเดตงบประมาณ
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $budget = Budget::findOrFail($id);
        $budget->update(['amount' => $request->amount]);

        return response()->json($budget, 200);
    }

    // ลบงบประมาณ
    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
