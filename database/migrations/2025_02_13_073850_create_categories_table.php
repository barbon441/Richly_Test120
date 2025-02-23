<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // ✅ เพิ่ม type เพื่อแยกหมวดหมู่
            $table->string('icon')->nullable();
            $table->timestamps();

            // ✅ ทำให้ `name` ซ้ำกันได้ แต่ต้อง `unique` คู่กับ `type`
            $table->unique(['name', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
