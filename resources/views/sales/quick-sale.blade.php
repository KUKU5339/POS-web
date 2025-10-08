@extends('layout')

@section('content')
<style>
    .quick-sale-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .product-card {
        background: #fff;
        border: 2px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .product-card:hover {
        border-color: #FFD700;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .product-card.out-of-stock {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .product-card.out-of-stock:hover {
        transform: none;
        border-color: #ddd;
    }

    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #f0f0f0;
    }

    .product-name {
        font-weight: bold;
        color: #800000;
        margin: 8px 0;
        font-size: 16px;
    }

    .product-price {
        color: #008000;
        font-size: 18px;
        font-weight: bold;
    }

    .product-stock {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .cart-container {
        position: fixed;
        bottom: 0;
        left: 220px;
        right: 0;
        background: #fff;
        border-top: 3px solid #800000;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
        transition: left 0.3s ease-in-out;
    }

    .cart-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 15px 20px;
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .cart-items {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .cart-item {
        background: #f8f8f8;
        padding: 10px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 250px;
        border: 2px solid #ddd;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-weight: bold;
        color: #800000;
        font-size: 14px;
    }

    .cart-item-price {
        color: #008000;
        font-size: 13px;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-btn {
        background: #800000;
        color: #fff;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        background: #a00000;
    }

    .qty-display {
        min-width: 30px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

    .remove-btn {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    }

    .remove-btn:hover {
        background: #c82333;
    }

    .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 2px solid #eee;
    }

    .cart-total {
        font-size: 24px;
        font-weight: bold;
        color: #800000;
    }

    .cart-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-clear {
        background: #6c757d;
        color: #fff;
    }

    .btn-clear:hover {
        background: #5a6268;
    }

    .btn-checkout {
        background: #FFD700;
        color: #800000;
    }

    .btn-checkout:hover {
        background: #e6c200;
        transform: scale(1.05);
    }

    .empty-cart {
        text-align: center;
        color: #999;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .cart-container {
            left: 0;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .product-image {
            height: 100px;
        }

        .cart-items {
            flex-direction: column;
        }

        .cart-item {
            min-width: 100%;
        }
    }
</style>

<div class="quick-sale-container">
    <div class="page-header">
        <div>
            <h2 style="color:#800000; margin:0;">⚡ Quick Sale Mode</h2>
            <p style="color:#666; margin:5px 0 0 0;">Tap products to add to cart</p>
        </div>
    </div>

    @if($products->isEmpty())
    <div style="text-align:center; padding:40px; background:#fff; border-radius:10px;">
        <i class="fas fa-box-open" style="font-size:48px; color:#ccc;"></i>
        <p style="color:#999; margin-top:15px;">No products available. <a href="{{ route('products.index') }}">Add some products first</a></p>
    </div>
    @else
    <div class="products-grid">
        @foreach($products as $product)
        <div class="product-card {{ $product->stock == 0 ? 'out-of-stock' : '' }}"
            onclick="{{ $product->stock > 0 ? 'addToCart('.$product->id.', \''.$product->name.'\', '.$product->price.', '.$product->stock.')' : '' }}">
            @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
            @else
            <div class="product-image" style="display:flex; align-items:center; justify-content:center; background:#f0f0f0;">
                <i class="fas fa-utensils" style="font-size:40px; color:#ccc;"></i>
            </div>
            @endif
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
            <div class="product-stock">
                @if($product->stock == 0)
                <span style="color:red;">Out of Stock</span>
                @else
                Stock: {{ $product->stock }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Shopping Cart -->
<div class="cart-container">
    <div class="cart-content">
        <div class="cart-header">
            <h3 style="margin:0; color:#800000;">🛒 Current Order</h3>
            <span id="cart-count" style="color:#666;">0 items</span>
        </div>

        <div id="cart-items" class="cart-items">
            <div class="empty-cart">Cart is empty. Tap products above to add items.</div>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                Total: <span id="cart-total">₱0.00</span>
            </div>
            <div class="cart-actions">
                <button class="btn btn-clear" onclick="clearCart()">Clear Cart</button>
                <button class="btn btn-checkout" onclick="checkout()" id="checkout-btn" disabled>Complete Sale</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    function addToCart(id, name, price, maxStock) {
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            if (existingItem.quantity < maxStock) {
                existingItem.quantity++;
            } else {
                alert('Maximum stock reached for ' + name);
                return;
            }
        } else {
            cart.push({
                id,
                name,
                price,
                quantity: 1,
                maxStock
            });
        }

        updateCart();
    }

    function updateQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (!item) return;

        item.quantity += change;

        if (item.quantity <= 0) {
            removeFromCart(id);
        } else if (item.quantity > item.maxStock) {
            item.quantity = item.maxStock;
            alert('Maximum stock reached');
        }

        updateCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
    }

    function updateCart() {
        const cartItemsDiv = document.getElementById('cart-items');
        const cartCount = document.getElementById('cart-count');
        const cartTotal = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('checkout-btn');

        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<div class="empty-cart">Cart is empty. Tap products above to add items.</div>';
            cartCount.textContent = '0 items';
            cartTotal.textContent = '₱0.00';
            checkoutBtn.disabled = true;
            return;
        }

        let html = '';
        let total = 0;
        let itemCount = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            itemCount += item.quantity;

            html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">₱${item.price.toFixed(2)} × ${item.quantity} = ₱${subtotal.toFixed(2)}</div>
                </div>
                <div class="quantity-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span class="qty-display">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
                <button class="remove-btn" onclick="removeFromCart(${item.id})">✕</button>
            </div>
        `;
        });

        cartItemsDiv.innerHTML = html;
        cartCount.textContent = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
        cartTotal.textContent = `₱${total.toFixed(2)}`;
        checkoutBtn.disabled = false;
    }

    function clearCart() {
        if (cart.length === 0) return;

        if (confirm('Clear all items from cart?')) {
            cart = [];
            updateCart();
        }
    }

    function checkout() {
        if (cart.length === 0) return;

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("sales.store") }}';

        // CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Add cart data
        const cartInput = document.createElement('input');
        cartInput.type = 'hidden';
        cartInput.name = 'cart';
        cartInput.value = JSON.stringify(cart);
        form.appendChild(cartInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>

@endsection