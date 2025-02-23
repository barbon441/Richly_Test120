<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount_limit',
        'amount_spent', // ✅ เพิ่มให้สามารถอัปเดตค่าได้
        'start_date',
        'end_date',
    ];

    /**
     * ความสัมพันธ์กับ Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id', 'category_id')
                    ->whereBetween('date', [$this->start_date, $this->end_date]);
    }

    /**
     * อัปเดต amount_spent อัตโนมัติเมื่อธุรกรรมมีการเปลี่ยนแปลง
     */
    public function updateAmountSpent()
    {
        $this->amount_spent = $this->transactions()->sum('amount');
        $this->save();
    }

    /**
     * Boot Method สำหรับอัปเดตงบประมาณเมื่อมีการเปลี่ยนแปลงธุรกรรม
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($budget) {
            $budget->updateAmountSpent();
        });
    }
}
