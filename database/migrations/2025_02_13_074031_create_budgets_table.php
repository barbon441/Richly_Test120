<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_limit', 10, 2); // ✅ วงเงินที่กำหนด
            $table->decimal('amount_spent', 10, 2)->default(0); // ✅ เพิ่มฟิลด์นี้เพื่อติดตามค่าใช้จ่าย
            $table->date('start_date'); // ✅ วันที่เริ่มต้นของงบประมาณ
            $table->date('end_date'); // ✅ วันที่สิ้นสุดของงบประมาณ
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('budgets');
    }
};
