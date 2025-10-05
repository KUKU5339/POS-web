@extends('layout')

@section('content')
    <h2>Dashboard</h2>
    <p style="font-size:16px; color:#555;">Control Panel</p> <!-- Subtitle -->

    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
        <div style="flex:1; background:#FFE4B5; padding:20px; border-radius:10px; text-align:center; box-shadow:2px 2px 8px rgba(0,0,0,0.1);">
            <h3>Total Sales</h3>
            <p style="font-size:24px; font-weight:bold; color:#800000;">₱{{ number_format($totalSales, 2) }}</p>
        </div>

        <div style="flex:1; background:#E0FFFF; padding:20px; border-radius:10px; text-align:center; box-shadow:2px 2px 8px rgba(0,0,0,0.1);">
            <h3>Products</h3>
            <p style="font-size:24px; font-weight:bold; color:#008080;">{{ $totalProducts }}</p>
        </div>

        <div style="flex:1; background:#F0E68C; padding:20px; border-radius:10px; text-align:center; box-shadow:2px 2px 8px rgba(0,0,0,0.1);">
            <h3>Items Sold</h3>
            <p style="font-size:24px; font-weight:bold; color:#DAA520;">{{ $totalItemsSold }}</p>
        </div>

        <div style="flex:1; background:#D8BFD8; padding:20px; border-radius:10px; text-align:center; box-shadow:2px 2px 8px rgba(0,0,0,0.1);">
            <h3>Low Stock</h3>
            <p style="font-size:24px; font-weight:bold; color:#800080;">{{ $lowStock->count() }}</p>
        </div>
    </div>

    <h3 style="margin-bottom:10px; color:#800000;">Recent Sales</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; margin-bottom:30px; border-collapse:collapse;">
        <tr style="background:#FFD700; color:#800000;">
            <th>Product</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
        @foreach($sales as $s)
            <tr style="background:#FFF8DC;">
                <td>{{ $s->product->name }}</td>
                <td>{{ $s->quantity }}</td>
                <td>₱{{ number_format($s->total, 2) }}</td>
                <td>{{ $s->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </table>

    <h3 style="margin-bottom:10px; color:#800000;">Current Inventory / Low Stock</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; margin-bottom:30px; border-collapse:collapse;">
        <tr style="background:#FFD700; color:#800000;">
            <th>Product</th>
            <th>Stock</th>
        </tr>
        @foreach($lowStock as $product)
            <tr style="background:#FFF8DC;">
                <td>{{ $product->name }}</td>
                <td>{{ $product->stock }}</td>
            </tr>
        @endforeach
    </table>

    <h3 style="margin-bottom:10px; color:#800000;">Top 5 Best Sellers</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse; box-shadow: 2px 2px 8px rgba(0,0,0,0.1);">
        <tr style="background:#FFD700; color:#800000;">
            <th>Product</th>
            <th>Total Sold</th>
        </tr>
        @foreach($topProducts as $product)
            <tr style="background:#FFF8DC;">
                <td>{{ $product->name }}</td>
                <td>{{ $product->total_sold }}</td>
            </tr>
        @endforeach
    </table>
@endsection
