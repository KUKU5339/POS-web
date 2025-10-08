@extends('layout')

@section('content')
<style>
    .products-container {
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

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #800000;
        color: #fff;
    }

    .btn-primary:hover {
        background: #a00000;
    }

    .btn-add {
        background: #FFD700;
        color: #800000;
    }

    .btn-add:hover {
        background: #e6c200;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .product-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #f5f5f5;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
        color: #999;
        font-size: 48px;
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.95);
    }

    .stock-low {
        color: #c62828;
        border: 2px solid #c62828;
    }

    .stock-medium {
        color: #e65100;
        border: 2px solid #e65100;
    }

    .stock-good {
        color: #2e7d32;
        border: 2px solid #2e7d32;
    }

    .product-info {
        padding: 20px;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0 0 10px 0;
    }

    .product-price {
        font-size: 22px;
        font-weight: bold;
        color: #4CAF50;
        margin-bottom: 15px;
    }

    .product-actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit {
        flex: 1;
        background: #FFD700;
        color: #800000;
        padding: 10px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #e6c200;
    }

    .btn-delete {
        background: #dc3545;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    .empty-state {
        background: #fff;
        padding: 60px 20px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }
</style>

<div class="products-container">
    <div class="page-header">
        <h2>📦 Products Management</h2>
        <p>Control your menu, stock, and pricing</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error">
        <strong>Validation Errors:</strong>
        <ul style="margin: 5px 0 0 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    @endif

    <div class="toolbar">
        <form method="GET" action="{{ route('products.index') }}" class="search-box">
            <input type="text" name="search" placeholder="🔍 Search products..."
                value="{{ request('search') }}" class="search-input">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <button onclick="openAddSidebar()" class="btn btn-add">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>

    @if($products->isEmpty())
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>No Products Found</h3>
        <p>
            @if(request('search'))
            No results for "{{ request('search') }}"
            @else
            Start by adding your first product
            @endif
        </p>
    </div>
    @else
    <div class="products-grid">
        @foreach($products as $product)
        <div class="product-card">
            <div class="product-image-container">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                    alt="{{ $product->name }}" class="product-image">
                @else
                <div class="product-image-placeholder">
                    <i class="fas fa-utensils"></i>
                </div>
                @endif

                <span class="stock-badge {{ $product->stock <= 5 ? 'stock-low' : ($product->stock <= 15 ? 'stock-medium' : 'stock-good') }}">
                    {{ $product->stock }} in stock
                </span>
            </div>

            <div class="product-info">
                <h3 class="product-name">{{ $product->name }}</h3>
                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>

                <div class="product-actions">
                    <button onclick="openEditSidebar({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->image }}')"
                        class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete"
                            onclick="return confirm('Delete {{ $product->name }}?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()"></div>

<!-- Add Sidebar -->
<div id="addSidebar" style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff; border-left:4px solid #800000; box-shadow:-2px 0 8px rgba(0,0,0,0.3); padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">➕ Add Product</h3>
    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:12px;">
            <label><b>Name:</b></label>
            <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Price:</b></label>
            <input type="number" step="0.01" name="price" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Stock:</b></label>
            <input type="number" name="stock" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Image:</b></label>
            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#800000; color:#fff; border:none; border-radius:5px; cursor:pointer;">Save</button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Cancel</button>
    </form>
</div>

<!-- Edit Sidebar -->
<div id="editSidebar" style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff; border-left:4px solid #FFD700; box-shadow:-2px 0 8px rgba(0,0,0,0.3); padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">✏ Edit Product</h3>
    <form id="editForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div style="margin-bottom:12px;">
            <label><b>Name:</b></label>
            <input type="text" id="editName" name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Price:</b></label>
            <input type="number" step="0.01" id="editPrice" name="price" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Stock:</b></label>
            <input type="number" id="editStock" name="stock" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Current Image:</b></label><br>
            <img id="editImagePreview" src="" alt="No image" style="width:100px; height:100px; object-fit:cover; margin-bottom:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Change Image:</b></label>
            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#FFD700; color:#800000; border:none; border-radius:5px; cursor:pointer;">Update</button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Cancel</button>
    </form>
</div>

<script>
    function openAddSidebar() {
        document.getElementById("addSidebar").style.right = "0";
        document.getElementById("overlay").style.display = "block";
    }

    function openEditSidebar(id, name, price, stock, image) {
        document.getElementById("editSidebar").style.right = "0";
        document.getElementById("overlay").style.display = "block";

        document.getElementById("editName").value = name;
        document.getElementById("editPrice").value = price;
        document.getElementById("editStock").value = stock;
        document.getElementById("editForm").action = "/products/" + id;

        const preview = document.getElementById("editImagePreview");
        if (image && image !== 'null') {
            preview.src = "/storage/" + image;
        } else {
            preview.src = "";
            preview.alt = "No image";
        }
    }

    function closeSidebars() {
        document.getElementById("addSidebar").style.right = "-100%";
        document.getElementById("editSidebar").style.right = "-100%";
        document.getElementById("overlay").style.display = "none";
    }
</script>

@endsection