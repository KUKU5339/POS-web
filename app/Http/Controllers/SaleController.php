<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $products = Product::where('user_id', auth()->id())->get();

        return view('sales.index', compact('sales', 'products'));
    }

    public function store(Request $request)
    {
        // Check if this is a cart-based sale (from Quick Sale Mode)
        if ($request->has('cart')) {
            $cartData = json_decode($request->cart, true);

            if (empty($cartData)) {
                return back()->withErrors(['cart' => 'Cart is empty!']);
            }

            $sales = [];

            foreach ($cartData as $item) {
                $product = Product::where('id', $item['id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$product) continue;

                $quantity = (int) $item['quantity'];

                if ($quantity > $product->stock) {
                    return back()->withErrors(['quantity' => "Not enough stock for {$product->name}!"]);
                }

                $total = $product->price * $quantity;

                $sale = Sale::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'total'      => $total,
                ]);

                $product->decrement('stock', $quantity);
                $sales[] = $sale->id;
            }

            // Redirect to receipt for the first sale (or create a combined receipt)
            return redirect()->route('sales.receipt', $sales[0])->with('success', 'Sale completed!');
        }

        // Original single-product sale logic
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            return back()->withErrors(['quantity' => 'Not enough stock!']);
        }

        $total = $product->price * $quantity;

        $sale = Sale::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'total'      => $total,
        ]);

        $product->decrement('stock', $quantity);

        return redirect()->route('sales.receipt', $sale->id);
    }

    public function quickSale()
    {
        $products = Product::where('user_id', auth()->id())
            ->where('stock', '>', 0)
            ->get();

        return view('sales.quick-sale', compact('products'));
    }

    public function generateReceipt($saleId)
    {
        $sale = Sale::with('product')
            ->where('id', $saleId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        return view('receipt', compact('sale'));
    }

    public function downloadReceiptPdf($saleId)
    {
        $sale = Sale::with('product')
            ->where('id', $saleId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        $pdf = Pdf::loadView('receipt', compact('sale'));
        return $pdf->download('receipt.pdf');
    }
}
