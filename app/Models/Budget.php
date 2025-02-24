<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount_limit',
        'amount_spent', // ✅ ค่าที่ถูกอัปเดตจาก Controller
        'start_date',
        'end_date',
    ];

    /**
     * ความสัมพันธ์กับ Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id', 'category_id');
    }
}
