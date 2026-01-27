<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Basic Counts
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();

        // 2. Specific Stats
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        $ordersPending = Order::where('status', 'pending')->count();
        $ordersCompleted = Order::where('status', 'completed')->count();

        // 3. Charts Data (Last 7 days)
        $ordersChart = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $usersChart = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'overview' => [
                'users' => $totalUsers,
                'products' => $totalProducts,
                'categories' => $totalCategories,
                'type' => 'overview'
            ],
            'orders_stats' => [ // Naming to differentiate from total_orders
                'total' => $totalOrders,
                'pending' => $ordersPending,
                'completed' => $ordersCompleted,
                'revenue' => $totalRevenue
            ],
            'charts' => [
                'orders' => $ordersChart,
                'users' => $usersChart
            ]
        ]);
    }
}
