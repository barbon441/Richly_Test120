<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
    $type = $request->query('type');
    $categories = Category::where('type', $type)->get();

    return response()->json($categories);
    }
}
