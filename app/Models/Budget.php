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
        'amount_spent',
        'start_date',
        'end_date',
    ];

    /**
     * ✅ เชื่อมโยงกับ User (เจ้าของงบประมาณ)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ เชื่อมโยงกับ Category (หมวดหมู่ของงบประมาณ)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * ✅ เชื่อมโยงกับ Transactions (รายการธุรกรรมที่เกี่ยวข้องกับงบประมาณ)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id', 'category_id')
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date]);
    }
}
