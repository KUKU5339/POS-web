<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 320px;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .items th, .items td {
            border-bottom: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .total {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-print {
            background-color: #4CAF50;
            border: none;
        }
        .btn-print:hover {
            background-color: #45a049;
        }
        .btn-pdf {
            background-color: #2196F3;
        }
        .btn-pdf:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Street Food POS</h2>
    <p>Sale ID: {{ $sale->id }}</p>
    <p>Date: {{ $sale->created_at->format('Y-m-d H:i') }}</p>
</div>

<table class="items">
    <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
    </tr>
    <tr>
        <td>{{ $sale->product->name }}</td>
        <td>{{ $sale->quantity }}</td>
        <td>{{ number_format($sale->product->price, 2) }}</td>
        <td>{{ number_format($sale->subtotal, 2) }}</td>
    </tr>
</table>

<p class="total">Total: {{ number_format($sale->total, 2) }}</p>

<div class="footer">
    <p>Thank you for your purchase!</p>
    <button class="btn btn-print" onclick="window.print();">Print Receipt</button>
    <a href="{{ route('sales.receipt.pdf', $sale->id) }}" class="btn btn-pdf">Download PDF</a>
</div>

</body>
</html>
