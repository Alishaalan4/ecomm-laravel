<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * GET /api/admin/orders
     */
    public function index()
    {
        $order = Order::with(['user','items.product'])->latest()->paginate(10);
        if (!$order)
        {
            return response()->json([
                "msg"=>"No orders found",
            ],404);
        }
        return response()->json([$order],200);
    }

    /**
     * GET /api/admin/orders/{id}
     */
    
    public function show($id)
    {
        $order = Order::with(['user','items.product'])->find($id);
        if (!$order)
        {
            return response()->json([
                "msg"=>"Order not found"
            ],404);
        }
        return response()->json([$order],200);
    }

    /**
     * PUT /api/admin/orders/{id}/status
     */
    public function update(Request $request,$id)
    {
        $order = Order::find($id);
        if (!$order)
        {
            return response()->json([
                "msg"=>"Order not found"
            ],404);
        }
        $validate = $request->validate([
            'status' => 'required|string|in:pending,completed,failed',
        ]);
        $order->update($validate);
        return response()->json([
            "msg"=>"Order status updated successfully"
        ],200);
    }

    
}
