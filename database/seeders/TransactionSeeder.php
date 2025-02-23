<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        Transaction::factory(50)->create(); // สร้างธุรกรรม 50 รายการ
    }
}
