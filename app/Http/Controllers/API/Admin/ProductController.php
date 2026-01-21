<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    public function index()
    {
        return Product::with('category')->latest()->paginate(10);
    }
    public function show($id)
    {
        return Product::with('category')->find($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'description' => 'required|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'size'        => 'nullable|string',
            'image'       => 'nullable|image'
        ]);


        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('products', 'public');
        }

        return Product::create($validated); 
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);  
        if (!$product)
        {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        $validated = $request->validate([
            'name'        => 'sometimes|string',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric',
            'stock'       => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'size'        => 'sometimes|string',
            'image'       => 'sometimes|image'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('products', 'public');      
        }

        $product->update($validated);
        return $product;
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product)
        {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    
    }
}
