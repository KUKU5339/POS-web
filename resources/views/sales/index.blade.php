@extends('layout')

@section('content')
<style>
    .sales-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h2 {
        color: #800000;
        margin: 0 0 5px 0;
        font-size: 28px;
    }

    .page-header p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add {
        background: #FFD700;
        color: #800000;
    }

    .btn-add:hover {
        background: #e6c200;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
    }

    .section-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table thead tr {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .modern-table th {
        padding: 12px;
        text-align: left;
        color: #800000;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
    }

    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .amount {
        color: #4CAF50;
        font-weight: 600;
    }

    .date-time {
        color: #666;
        font-size: 13px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .modern-table {
            font-size: 13px;
        }

        .modern-table th,
        .modern-table td {
            padding: 8px;
        }
    }
</style>

<div class="sales-container">
    <div class="page-header">
        <h2>📊 Sales History</h2>
        <p>Track your daily sales and transactions</p>
    </div>

    <div class="toolbar">
        <button onclick="openAddSaleSidebar()" class="btn btn-add">
            <i class="fas fa-plus"></i> New Sale
        </button>
    </div>

    <div class="section-card">
        @if($sales->isEmpty())
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <h3 style="color:#666; margin:0 0 10px 0;">No Sales Yet</h3>
            <p>Start by recording your first sale or use Quick Sale Mode</p>
        </div>
        @else
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td class="product-name">{{ $sale->product->name }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td class="amount">₱{{ number_format($sale->total, 2) }}</td>
                    <td class="date-time">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()"></div>

<!-- Add Sale Sidebar -->
<div id="addSaleSidebar" style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff; border-left:4px solid #800000; box-shadow:-2px 0 8px rgba(0,0,0,0.3); padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">➕ Record New Sale</h3>
    <form method="POST" action="{{ route('sales.store') }}">
        @csrf
        <div style="margin-bottom:12px;">
            <label><b>Product:</b></label>
            <select name="product_id" id="productSelect" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                <option value="" disabled selected>-- Select Product --</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                    {{ $product->name }} (₱{{ number_format($product->price, 2) }}) - {{ $product->stock }} in stock
                </option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Quantity:</b></label>
            <input type="number" id="quantityInput" name="quantity" min="1" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
            <small id="stockWarning" style="color:#e65100; font-size:12px; display:none;"></small>
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Total:</b></label>
            <input type="text" id="totalInput" readonly style="width:100%; padding:10px; background:#f8f8f8; border:1px solid #ccc; border-radius:5px; font-weight:bold; color:#4CAF50;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#800000; color:#fff; border:none; border-radius:5px; cursor:pointer; font-weight:600;">
            <i class="fas fa-save"></i> Save Sale
        </button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">
            Cancel
        </button>
    </form>
</div>

<script>
    function openAddSaleSidebar() {
        document.getElementById("addSaleSidebar").style.right = "0";
        document.getElementById("overlay").style.display = "block";
    }

    function closeSidebars() {
        document.getElementById("addSaleSidebar").style.right = "-100%";
        document.getElementById("overlay").style.display = "none";
    }

    const productSelect = document.getElementById("productSelect");
    const quantityInput = document.getElementById("quantityInput");
    const totalInput = document.getElementById("totalInput");
    const stockWarning = document.getElementById("stockWarning");

    function calculateTotal() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const price = selected ? parseFloat(selected.dataset.price) : 0;
        const stock = selected ? parseInt(selected.dataset.stock) : 0;
        const quantity = parseInt(quantityInput.value) || 0;

        // Show stock warning
        if (quantity > stock) {
            stockWarning.textContent = `⚠️ Only ${stock} items available`;
            stockWarning.style.display = 'block';
            quantityInput.style.borderColor = '#e65100';
        } else {
            stockWarning.style.display = 'none';
            quantityInput.style.borderColor = '#ccc';
        }

        // Calculate total
        if (price && quantity) {
            totalInput.value = "₱" + (price * quantity).toFixed(2);
        } else {
            totalInput.value = "";
        }
    }

    if (productSelect) {
        productSelect.addEventListener("change", function() {
            quantityInput.value = "1";
            calculateTotal();
        });
        quantityInput.addEventListener("input", calculateTotal);
    }
</script>

@endsection