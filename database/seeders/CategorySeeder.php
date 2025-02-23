<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'อาหาร', 'type' => 'expense', 'icon' => '🍔'],
            ['name' => 'การเดินทาง', 'type' => 'expense', 'icon' => '🚗'],
            ['name' => 'ที่อยู่อาศัย', 'type' => 'expense', 'icon' => '🏠'],
            ['name' => 'ของใช้', 'type' => 'expense', 'icon' => '🛒'],
            ['name' => 'อื่นๆ', 'type' => 'expense', 'icon' => '🛠️'], // ✅ Expense

            ['name' => 'เงินเดือน', 'type' => 'income', 'icon' => '💵'],
            ['name' => 'โบนัส', 'type' => 'income', 'icon' => '🎉'],
            ['name' => 'ธุรกิจ', 'type' => 'income', 'icon' => '🏢'],
            ['name' => 'ครอบครัว', 'type' => 'income', 'icon' => '👨‍👩‍👧‍👦'],
            ['name' => 'อื่นๆ', 'type' => 'income', 'icon' => '🛠️'], // ✅ Income
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name'], 'type' => $category['type']], // ✅ ตรวจสอบจาก name + type
                [
                    'icon' => $category['icon'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

