<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * GET /api/user/cart
     * Show user's cart
     */
    public function index(Request $request)
    {
        $cart = $request->user()->cart()->with('items.product')->first();
        if (!$cart)
        {
            return response()->json([ [] ]);
        }
        return response()->json($cart);
    }

    /**
     * POST /api/user/cart/add
     * Add product to cart
     * body: product_id, quantity
     */
    public function add(Request $request)
    {
        $validate = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);

        // 🔴 Load product
        $product = Product::findOrFail($validate['product_id']);

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {

            $newQuantity = $cartItem->quantity + $validate['quantity'];

            // 🔴 STOCK CHECK
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'msg' => 'Quantity exceeds available stock',
                    'available_stock' => $product->stock
                ], 422);
            }

            $cartItem->update([
                'quantity' => $newQuantity
            ]);

        } else {

            // 🔴 STOCK CHECK
            if ($validate['quantity'] > $product->stock) {
                return response()->json([
                    'msg' => 'Not enough stock available',
                    'available_stock' => $product->stock
                ], 422);
            }

            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validate['quantity'],
            ]);
        }

        return response()->json([
            "msg" => "Product added to cart successfully",
            "cart" => $cart->load('items.product')
    ]);
    }

    /**
     * PUT /api/user/cart/update
     * Update quantity of cart item
     * body: product_id, quantity
     */
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'product_id' =>'required|exists:products,id',
            'qunatity' => 'required|integer|min:1'
        ]);
        $cart = $request->user()->cart;
        if (!$cart)
        {
            return response()->json([
                "msg" => "cart not found",
            ],404);
        }
        $cartItems= $cart->items()->where('product_id',$validate['product_id'])->first();
        if (!$cartItems)
        {
            return response()->json([
                "msg" => "cart item not found",
            ],404);
        }
        $cartItems->quantity = $validate['qunatity'];
        $cartItems->save();
        return response()->json([
            "msg" => "cart item updated",
            $cartItems
        ]);
    }

    /**
     * DELETE /api/user/cart/remove/{id}
     * Remove single product from cart
     */
    public function remove(Request $request, string $id)
    {
        $cart = $request->user()->cart;
        if (!$cart)
        {
            return response()->json([
                "msg"=>"Cart not found",
            ],404);
        }
        $cartItem = $cart->items()->where('product_id',$id)->first();
        if (!$cartItem)
        {
            return response()->json([
                'msg'=> 'item not found',
            ],404);
        }

        $cartItem->delete();
        return response()->json([
            'msg'=> 'item removed from cart',
        ],200);

    }

    /**
     * DELETE /api/user/cart/clear
     * Remove all items from cart
     */
    public function clear(Request $request)
    {
        $cart = $request->user()->cart;
        if (!$cart)
        {
            return response()->json([
                "msg"=>"Cart not found",
            ],404);
        }
        $cart->items()->delete();
        return response()->json([
            "msg"=>"Cart cleared",
        ],200);
    }
}
