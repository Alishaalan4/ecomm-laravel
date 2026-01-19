<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;



/**
 * POST   /api/user/checkout
 */
class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty())
        {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        $validate = $request->validate([
            'payment_method' => 'required|in:cod,wish_money',
            'address'        => 'required_if:payment_method,cod|string|max:255',
            'phone_number'   => 'required_if:payment_method,wish_money|string|max:20',
        ]);

        $order = Order::create([
            'user_id'=>$user->id,
            'payment_method'=>$validate['payment_method'],
            'address'=>$validate['address'],
            'phone_number'=>$validate['phone_number'],
            'status'=>'pending',
            'total_price'=>$cart->items->sum(fn($item) => $item->quantity * $item->product->price)
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'=>$order->id,
                'product_id'=>$item->product_id,
                'quantity'=>$item->quantity,
                'price'=>$item->product->price
            ]);

            $item->product->decrement('stock',$item->quantity);
        }

        $cart->items()->delete();

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }


    /*
    * GET /api/user/orders
    * List all orders of authenticated user
    */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->get();
        return response()->json([$orders], 200);
    }
    

    /**
     * GET /api/user/orders/{id}
     * Show single order
     */
    public function show(Request $request,$id)
    {
        $order = $request->user()->orders()->with('items.product')->find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }
        return response()->json([$order], 200);
    }

}

// flow of checkout
/*

🧾 Checkout in Postman (Laravel API)

Your checkout endpoint is:

POST /api/user/checkout


It is:

🔒 Protected (auth:sanctum)

🛒 Requires items already in cart

💳 Requires payment_method

✅ 1️⃣ Login first (get token)
Request
POST http://127.0.0.1:8000/api/login

Body → raw → JSON
{
  "email": "user@example.com",
  "password": "password"
}

Response (example)
{
  "token": "1|eyJ0eXAiOiJKV1QiLCJhbGci..."
}


📌 Copy the token

✅ 2️⃣ Set Authorization in Postman

For ALL protected requests:

Authorization tab

Type: Bearer Token

Token:

1|eyJ0eXAiOiJKV1QiLCJhbGci...

✅ 3️⃣ Make sure cart has items
Add product to cart
POST http://127.0.0.1:8000/api/user/cart/add

Body → JSON
{
  "product_id": 3,
  "quantity": 2
}

Expected response
{
  "message": "Item added to cart"
}

✅ 4️⃣ Checkout (IMPORTANT PART)
Request
POST http://127.0.0.1:8000/api/user/checkout

🅰️ Cash On Delivery (COD)
Body → JSON
{
  "payment_method": "cod",
  "address": "Beirut, Hamra Street"
}

🅱️ Wish Money
Body → JSON
{
  "payment_method": "wish_money",
  "phone_number": "+96170123456"
}





{
  "message": "Checkout successful",
  "order": {
    "id": 12,
    "user_id": 5,
    "status": "pending",
    "payment_method": "cod",
    "total_price": 120,
    "items": [
      {
        "product_id": 3,
        "quantity": 2,
        "price": 60,
        "product": {
          "name": "iPhone Charger"
        }
      }
    ]
  }
}

*/