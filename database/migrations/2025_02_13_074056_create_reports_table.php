<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date'); // ✅ วันที่เริ่มต้นของรายงาน
            $table->date('end_date'); // ✅ วันที่สิ้นสุดของรายงาน
            $table->decimal('total_income', 10, 2)->default(0); // ✅ รายรับรวม
            $table->decimal('total_expense', 10, 2)->default(0); // ✅ รายจ่ายรวม
            $table->decimal('balance', 10, 2)->default(0); // ✅ ยอดคงเหลือ
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('reports');
    }
};
