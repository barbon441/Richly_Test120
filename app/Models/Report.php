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
     * ✅ เชื่อมโยงกับ User (เจ้าของรายงาน)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ เชื่อมโยงกับ Transaction (ธุรกรรมที่เกี่ยวข้องกับรายงาน)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id')
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date]);
    }
}
