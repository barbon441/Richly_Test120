<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'total_income',
        'total_expense',
        'balance',
    ];

    /**
     * คำนวณรายรับ-รายจ่าย และยอดคงเหลือ
     */
    public function calculateReport()
    {
        $transactions = Transaction::where('user_id', $this->user_id)
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->get();

        $totalIncome = $transactions->where('amount', '>', 0)->sum('amount');
        $totalExpense = $transactions->where('amount', '<', 0)->sum('amount');
        $balance = $totalIncome + $totalExpense;

        $this->update([
            'total_income' => $totalIncome,
            'total_expense' => abs($totalExpense),
            'balance' => $balance,
        ]);
    }

    /**
     * Boot Method สำหรับอัปเดตรายงานเมื่อมีการเปลี่ยนแปลงธุรกรรม
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($report) {
            $report->calculateReport();
        });
    }
}
