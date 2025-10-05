<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    // Show all sales and products
    public function index()
    {
        $sales = Sale::with('product')->latest()->get();
        $products = Product::all();

        return view('sales.index', compact('sales', 'products'));
    }

    // Record a new sale
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            return back()->withErrors(['quantity' => 'Not enough stock!']);
        }

        $total = $product->price * $quantity;

        // Create sale
        $sale = Sale::create([
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'total'      => $total,
        ]);

        // Decrease stock
        $product->decrement('stock', $quantity);

        // Redirect to HTML receipt view
        return redirect()->route('sales.receipt', $sale->id);
    }

    // Show HTML receipt
    public function generateReceipt($saleId)
    {
        $sale = Sale::with('product')->find($saleId);

        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        return view('receipt', compact('sale'));
    }

    // Download PDF receipt
    public function downloadReceiptPdf($saleId)
    {
        $sale = Sale::with('product')->find($saleId);

        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        $pdf = Pdf::loadView('receipt', compact('sale'));
        return $pdf->download('receipt.pdf');
    }
}
