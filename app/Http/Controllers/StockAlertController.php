<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StockAlertController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $threshold = $user->default_stock_threshold;

        // Get low stock products
        $lowStockProducts = Product::where('user_id', $user->id)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get();

        // Get out of stock products
        $outOfStock = Product::where('user_id', $user->id)
            ->where('stock', 0)
            ->get();

        // Get products approaching low stock (within 2 of threshold)
        $approachingLowStock = Product::where('user_id', $user->id)
            ->where('stock', '>', $threshold)
            ->where('stock', '<=', $threshold + 2)
            ->orderBy('stock', 'asc')
            ->get();

        return view('stock-alerts.index', compact(
            'lowStockProducts',
            'outOfStock',
            'approachingLowStock',
            'threshold'
        ));
    }

    public function updateThreshold(Request $request)
    {
        $request->validate([
            'threshold' => 'required|integer|min:1|max:100'
        ]);

        $user = auth()->user();
        $user->update([
            'default_stock_threshold' => $request->threshold
        ]);

        return back()->with('success', 'Stock alert threshold updated!');
    }

    public function generateShoppingList()
    {
        $user = auth()->user();
        $threshold = $user->default_stock_threshold;

        $lowStockProducts = Product::where('user_id', $user->id)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get();

        $pdf = Pdf::loadView('stock-alerts.shopping-list', compact('lowStockProducts', 'user'));
        return $pdf->download('shopping-list-' . date('Y-m-d') . '.pdf');
    }

    public function toggleAlerts(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'enable_stock_alerts' => !$user->enable_stock_alerts
        ]);

        $status = $user->enable_stock_alerts ? 'enabled' : 'disabled';
        return back()->with('success', "Stock alerts $status!");
    }
}
