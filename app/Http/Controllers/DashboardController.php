<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User; // <- add this

class DashboardController extends Controller
{
    public function index() {
        $totalSales = Sale::sum('total');
        $totalProducts = Product::count();
        $totalItemsSold = Sale::sum('quantity'); // total items sold
        $totalUsers = User::count(); // <- add this
        $lowStock = Product::where('stock', '<=', 5)->get(); // alert for low stock
        $products = Product::all(); // all products for table in dashboard
        $sales = Sale::with('product')->latest()->get(); // sales table

        // Top 5 Best Sellers
        $topProducts = Product::withSum('sales', 'quantity')
            ->orderBy('sales_sum_quantity', 'desc')
            ->take(5)
            ->get()
            ->map(function($p) {
                $p->total_sold = $p->sales_sum_quantity ?? 0;
                return $p;
            });

        return view('dashboard', compact(
            'totalSales',
            'totalProducts',
            'totalItemsSold',
            'totalUsers',  // <- add this
            'lowStock',
            'products',
            'sales',
            'topProducts'
        ));
    }
}
