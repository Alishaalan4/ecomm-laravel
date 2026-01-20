<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        if(!$category)
        {
            return response()->json([
                'message' => 'No categories found'
            ],404);
        }
        return response()->json($category);
    }

    /**
     *         $table->string('name');
     *         $table->string('slug')->unique();
     *         $table->string('description');
     */
    public function store(Request $request)
    {
        // check if category found
        $category = Category::where('slug', $request->slug)->first();
        if($category)
        {
            return response()->json([
                'message' => 'Category already exists'
            ],400);
        }
        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description
        ]);
        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ],201);
    }

    public function create(Request $request)
    {
        // create category
        $category = Category::create($request->all());
        return response()->json([$category],201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if(!$category)
        {
            return response()->json([
                'message' => 'Category not found'
            ],404);
        }
        $category->update($request->all());
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ],200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category)
        {
            return response()->json([
                'message' => 'Category not found'
            ],404);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ],200);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if(!$category)
        {
            return response()->json([
                'message' => 'Category not found'
            ],404);
        }
        return response()->json($category);
    }

}
