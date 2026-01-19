<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        if (!$category)
        {
            return response()->json([
                'msg' => 'No categories found'
            ],404);
        }
        return response()->json($category,200);
    }

    /*
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category)
        {
            return response()->json([
                'msg' => 'Category not found'
            ],404);
        }
        return response()->json($category,200);
    }

}
