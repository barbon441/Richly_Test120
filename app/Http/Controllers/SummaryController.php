<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function getSummaryData(Request $request)
{
    // ✅ ตรวจสอบว่ามี user ที่ล็อกอินอยู่หรือไม่
    if (!$request->user()) {
        return response()->json(['message' => 'กรุณาเข้าสู่ระบบ'], 401);
    }
    $userId = Auth::id(); // ✅ ดึง ID ของผู้ใช้ที่ล็อกอิน
    $type = $request->query('type', 'expense');

    $transactions = Transaction::where('transaction_type', $type)
        ->where('user_id', $userId) // ✅ กรองตาม user_id
        ->join('categories', 'transactions.category_id', '=', 'categories.id')
        ->selectRaw('categories.name as category, SUM(transactions.amount) as total')
        ->groupBy('categories.name')
        ->get();

    if ($transactions->isEmpty()) {
        return response()->json(['message' => 'ไม่พบข้อมูลสำหรับผู้ใช้นี้'], 404);
    }

    return response()->json($transactions);
}

}
