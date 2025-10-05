@extends('layout')

@section('content')
    <h2 style="color:#800000; margin-bottom:10px;">Sales History</h2>
    <h4 style="color:#555; margin-bottom:20px;">Track your daily sales and transactions</h4>

    <!-- New Sale Button -->
    <button onclick="openAddSaleSidebar()"
            style="padding:10px 20px; background:#FFD700; color:#800000; font-weight:bold;
                   border:none; border-radius:5px; cursor:pointer;">
        ➕ New Sale
    </button>

    <!-- Sales Table -->
    <table cellpadding="10" cellspacing="0"
           style="width:100%; margin-top:20px; border-collapse:collapse; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <tr style="background:#800000; color:#fff; text-align:left;">
            <th>Product</th>
            <th>Quantity</th>
            <th>Total (₱)</th>
            <th>Date</th>
        </tr>
        @forelse($sales as $s)
            <tr style="border-bottom:1px solid #ddd; background:#fff;">
                <td style="font-weight:bold; color:#333;">{{ $s->product->name }}</td>
                <td style="color:#444;">{{ $s->quantity }}</td>
                <td style="color:#008000; font-weight:bold;">₱{{ number_format($s->total, 2) }}</td>
                <td style="color:#666;">{{ $s->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#777;">
                    No sales recorded yet.
                </td>
            </tr>
        @endforelse
    </table>

    <!-- Overlay -->
    <div id="overlay"
         style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
           background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()">
    </div>

    <!-- Add Sale Sidebar -->
    <div id="addSaleSidebar"
         style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff;
           border-left:4px solid #800000; box-shadow:-2px 0 8px rgba(0,0,0,0.3);
           padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
        <h3 style="color:#800000; margin-bottom:15px;">➕ Record New Sale</h3>
        <form method="POST" action="{{ route('sales.store') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label><b>Product:</b></label>
                <select name="product_id" id="productSelect" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                    <option value="" disabled selected>-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} (₱{{ number_format($product->price, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:12px;">
                <label><b>Quantity:</b></label>
                <input type="number" id="quantityInput" name="quantity" min="1" required
                       style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
            </div>
            <div style="margin-bottom:12px;">
                <label><b>Total:</b></label>
                <input type="text" id="totalInput" name="total" readonly
                       style="width:100%; padding:10px; background:#f8f8f8; border:1px solid #ccc; border-radius:5px;">
            </div>
            <button type="submit"
                    style="padding:10px 20px; background:#800000; color:#fff; border:none;
                           border-radius:5px; cursor:pointer;">
                Save
            </button>
            <button type="button" onclick="closeSidebars()"
                    style="padding:10px 20px; background:#ccc; color:#000; border:none;
                           border-radius:5px; cursor:pointer;">
                Cancel
            </button>
        </form>
    </div>

    <!-- JS for Sidebar & Auto Total -->
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

        function calculateTotal() {
            const selected = productSelect.options[productSelect.selectedIndex];
            const price = selected ? selected.dataset.price : 0;
            const quantity = quantityInput.value;
            if (price && quantity) {
                totalInput.value = "₱" + (price * quantity).toFixed(2);
            } else {
                totalInput.value = "";
            }
        }

        if (productSelect) {
            productSelect.addEventListener("change", calculateTotal);
            quantityInput.addEventListener("input", calculateTotal);
        }
    </script>
@endsection
