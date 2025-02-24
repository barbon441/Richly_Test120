<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all(); // ✅ ดึงหมวดหมู่ทั้งหมด
        return response()->json($categories);
    }

}
