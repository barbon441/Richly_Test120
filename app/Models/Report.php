<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type', // 'daily', 'weekly', 'monthly', 'yearly'
        'total_income',
        'total_expense',
        'report_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
