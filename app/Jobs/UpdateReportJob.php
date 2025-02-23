<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class UpdateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $transactionDate;

    public function __construct($userId, $transactionDate)
    {
        $this->userId = $userId;
        $this->transactionDate = $transactionDate;
    }

    public function handle()
    {
        try {
            Log::info("📊 [QUEUE] อัปเดตรายงานสำหรับผู้ใช้ $this->userId วันที่ $this->transactionDate");

            $startDate = date('Y-m-01', strtotime($this->transactionDate));
            $endDate = date('Y-m-t', strtotime($this->transactionDate));

            $transactions = Transaction::where('user_id', $this->userId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->selectRaw("
                    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) as total_expense
                ")
                ->first();

            $totalIncome = $transactions->total_income ?? 0;
            $totalExpense = $transactions->total_expense ?? 0;
            $balance = $totalIncome - $totalExpense;

            Report::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'balance' => $balance,
                ]
            );

            Log::info("✅ [QUEUE] รายงานอัปเดตสำเร็จ: รายรับ = $totalIncome, รายจ่าย = $totalExpense, คงเหลือ = $balance");

        } catch (\Exception $e) {
            Log::error("❌ [QUEUE] Error ในการอัปเดตรายงาน: " . $e->getMessage());
        }
    }
}

