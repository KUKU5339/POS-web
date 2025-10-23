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
            $cartData = $request->input('cart');

            // Handle JSON requests
            if (is_string($cartData)) {
                $cartData = json_decode($cartData, true);
            }

            if (empty($cartData)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Cart is empty!'], 400);
                }
                return back()->withErrors(['cart' => 'Cart is empty!']);
            }

            $sales = [];
            $totalAmount = 0;

            foreach ($cartData as $item) {
                $product = Product::where('id', $item['id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$product) continue;

                $quantity = (int) $item['quantity'];

                if ($quantity > $product->stock) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Not enough stock for {$product->name}!"
                        ], 400);
                    }
                    return back()->withErrors(['quantity' => "Not enough stock for {$product->name}!"]);
                }

                $total = $product->price * $quantity;
                $totalAmount += $total;

                $sale = Sale::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'total'      => $total,
                ]);

                $product->decrement('stock', $quantity);
                $sales[] = $sale->id;
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully!',
                    'total' => $totalAmount,
                    'sales_count' => count($sales),
                    'sale_id' => $sales[0] ?? null  // Return first sale ID for receipt
                ]);
            }

            // Regular redirect for form submissions
            return redirect()->route('sales.receipt', $sales[0])
                ->with('success', 'Sale completed!')
                ->with('sale_notification', [
                    'total' => $totalAmount,
                    'items' => count($cartData)
                ]);
        }

        // Original single-product sale logic (at the bottom of store method)
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock!'
                ], 400);
            }
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

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sale saved successfully!',
                'total' => $total,
                'sale_id' => $sale->id  // Return sale ID for receipt
            ]);
        }

        // Regular redirect for non-AJAX
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

    public function syncOfflineSale(Request $request)
    {
        try {
            $saleData = $request->all();

            // Validate the incoming data
            if (!isset($saleData['cart']) || empty($saleData['cart'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart data provided'
                ], 400);
            }

            $cartData = $saleData['cart'];
            if (is_string($cartData)) {
                $cartData = json_decode($cartData, true);
            }

            $sales = [];
            $totalAmount = 0;

            foreach ($cartData as $item) {
                $product = Product::where('id', $item['id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$product) {
                    continue;
                }

                $quantity = (int) $item['quantity'];

                // Check stock availability
                if ($quantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough stock for {$product->name}. Available: {$product->stock}"
                    ], 400);
                }

                $total = $product->price * $quantity;
                $totalAmount += $total;

                // Create the sale
                $sale = Sale::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'total'      => $total,
                ]);

                // Update stock
                $product->decrement('stock', $quantity);
                $sales[] = $sale->id;
            }

            return response()->json([
                'success' => true,
                'message' => 'Offline sale synced successfully',
                'total' => $totalAmount,
                'sales_count' => count($sales),
                'sale_ids' => $sales
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync sale: ' . $e->getMessage()
            ], 500);
        }
    }
}
