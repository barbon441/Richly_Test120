<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        $transactions = Transaction::with('category') // ดึงข้อมูลจากตาราง categories
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id'                => $transaction->id,
                    'category'          => $transaction->category->name ?? 'ไม่ระบุหมวดหมู่',
                    'icon'              => $transaction->category->icon ?? '❓',
                    'description'       => $transaction->description ?? 'ไม่มีรายละเอียด',
                    'amount'            => $transaction->amount,
                    'transaction_type'  => $transaction->transaction_type,
                    'date'              => $transaction->transaction_date,
                    'created_at'        => $transaction->created_at,
                ];
            });

        return response()->json(['transactions' => $transactions]);
    }

    /**
     * Store a newly created transaction.
     */
    //อ่านข้อมูลจาก Request และบันทึกข้อมูลลงฐานข้อมูล
    public function store(Request $request)
    {
        $startTime = microtime(true); // ⏳ เริ่มจับเวลา

        try {
            Log::info("📥 Data received in Backend:", $request->all());

            // ✅ ตรวจสอบข้อมูล
            $validatedData = $request->validate([
                'amount' => 'required|numeric',
                'transaction_type' => 'required|in:income,expense',
                'category_id' => 'required|exists:categories,id',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
            ]);

            // ✅ ดึง user_id
            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // ✅ ตรวจสอบหมวดหมู่
            $category = Category::find($validatedData['category_id']);
            if (!$category || $category->type !== $validatedData['transaction_type']) {
                return response()->json([
                    'success' => false,
                    'message' => 'หมวดหมู่ไม่ตรงกับประเภทของธุรกรรม'
                ], 400);
            }

            // ✅ บันทึกธุรกรรม
            $transaction = Transaction::create([
                'user_id'           => $userId,
                'category_id'       => $category->id,
                'amount'            => $validatedData['amount'],
                'transaction_type'  => $validatedData['transaction_type'],
                'description'       => $validatedData['description'] ?? null,
                'transaction_date'  => $validatedData['transaction_date'],
            ]);

            Log::info("✅ ธุรกรรมถูกบันทึกสำเร็จ:", $transaction->toArray());

            // ⏳ จับเวลาการอัปเดตรายงาน
            $reportStartTime = microtime(true);
            $this->updateReport($userId, $validatedData['transaction_date']);
            $reportEndTime = microtime(true);
            Log::info("⏳ เวลาในการอัปเดตรายงาน: " . round(($reportEndTime - $reportStartTime), 3) . " วินาที");

            $endTime = microtime(true); // ⏳ สิ้นสุดการจับเวลา
            Log::info("⏳ เวลาทำงานทั้งหมดของ store(): " . round(($endTime - $startTime), 3) . " วินาที");

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มธุรกรรมสำเร็จ!',
                'transaction' => $transaction
            ], 201);

        } catch (\Exception $e) {
            Log::error("❌ Error ในการบันทึกธุรกรรม: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }




    /**
     * ✅ ฟังก์ชันสำหรับอัปเดตรายงานในตาราง `reports`
     */
    private function updateReport($userId, $transactionDate)
    {
        try {
            Log::info("📊 กำลังอัปเดตรายงานสำหรับผู้ใช้ $userId วันที่ $transactionDate");

            // ✅ คำนวณรายรับและรายจ่ายใหม่
            $totalIncome = Transaction::where('user_id', $userId)
                ->where('transaction_type', 'income')
                ->whereBetween('transaction_date', [date('Y-m-01', strtotime($transactionDate)), date('Y-m-t', strtotime($transactionDate))])
                ->sum('amount');

            $totalExpense = Transaction::where('user_id', $userId)
                ->where('transaction_type', 'expense')
                ->whereBetween('transaction_date', [date('Y-m-01', strtotime($transactionDate)), date('Y-m-t', strtotime($transactionDate))])
                ->sum('amount');

            $balance = $totalIncome - $totalExpense;

            // ✅ ค้นหาหรือสร้าง `report`
            $report = Report::updateOrCreate(
                [
                    'user_id' => $userId,
                    'start_date' => date('Y-m-01', strtotime($transactionDate)),
                    'end_date' => date('Y-m-t', strtotime($transactionDate)),
                ],
                [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'balance' => $balance,
                ]
            );

            Log::info("📊 รายงานอัปเดตสำเร็จ: รายรับ = $totalIncome, รายจ่าย = $totalExpense, คงเหลือ = $balance");

        } catch (\Exception $e) {
            Log::error("❌ Error ในการอัปเดตรายงาน: " . $e->getMessage());
            throw new \Exception("❌ เกิดข้อผิดพลาดขณะอัปเดตรายงาน: " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $transaction = Transaction::find($id);
    if (!$transaction) {
        return response()->json(['message' => 'ไม่พบธุรกรรม'], 404);
    }

    $validated = $request->validate([
        'category_id'      => 'required|integer|exists:categories,id',
        'amount'           => 'required|numeric',
        'transaction_type' => 'required|string',
        'description'      => 'nullable|string',
        'transaction_date' => 'required|date',
    ]);

    // ✅ ใช้หมวดหมู่ร่วมกัน (ไม่ต้องเช็ค user_id)
    $category = Category::find($validated['category_id']);

    if (!$category) {
        return response()->json(['success' => false, 'message' => 'หมวดหมู่ที่เลือกไม่ถูกต้อง'], 400);
    }

    // ✅ อัปเดตรายการธุรกรรม
    $transaction->update([
        'category_id'      => $category->id,
        'amount'           => $validated['amount'],
        'transaction_type' => $validated['transaction_type'],
        'description'      => $validated['description'],
        'transaction_date' => $validated['transaction_date'],
    ]);

    return response()->json([
        'message'     => 'อัปเดตรายการสำเร็จ',
        'transaction' => $transaction,
        'category'    => $category,
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'ไม่พบธุรกรรม'], 404);
        }

        $transaction->delete();
        return response()->json(['message' => 'ลบธุรกรรมสำเร็จ']);
    }
}
