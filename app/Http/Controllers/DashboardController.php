<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Only count/sum data belonging to the current user
        $totalSales = Sale::where('user_id', $userId)->sum('total');
        $totalProducts = Product::where('user_id', $userId)->count();
        $totalItemsSold = Sale::where('user_id', $userId)->sum('quantity');

        $lowStock = Product::where('user_id', $userId)
            ->where('stock', '<=', 5)
            ->get();

        $products = Product::where('user_id', $userId)->get();

        $sales = Sale::with('product')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        // Top 5 Best Sellers - only from this user's products
        $topProducts = Product::where('user_id', $userId)
            ->withSum('sales', 'quantity')
            ->orderBy('sales_sum_quantity', 'desc')
            ->take(5)
            ->get()
            ->map(function ($p) {
                $p->total_sold = $p->sales_sum_quantity ?? 0;
                return $p;
            });

        // Today's sales for daily summary notification
        $todaySales = Sale::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        $todayRevenue = Sale::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->sum('total');

        return view('dashboard', compact(
            'totalSales',
            'totalProducts',
            'totalItemsSold',
            'lowStock',
            'products',
            'sales',
            'topProducts',
            'todaySales',
            'todayRevenue'
        ));
    }
}
