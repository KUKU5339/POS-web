@extends('layout')

@section('content')
    <h2 style="color:#800000; margin-bottom:10px;">Products Management</h2>
    <h4 style="color:#555; margin-bottom:20px;">Control your menu, stock, and pricing</h4>

    <!-- Search Form -->
    <form method="GET" action="{{ route('products.index') }}"
          style="margin-bottom:20px; display:flex; gap:10px; align-items:center;">
        <input type="text" name="search" placeholder="🔍 Search product..."
               value="{{ request('search') }}"
               style="flex:1; padding:10px; border:1px solid #ccc; border-radius:5px;">
        <button type="submit"
                style="padding:10px 20px; background:#800000; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            Search
        </button>
    </form>

    <!-- Add Product Button -->
    <button onclick="openAddSidebar()"
            style="padding:10px 20px; background:#FFD700; color:#800000;
                   font-weight:bold; border:none; border-radius:5px; cursor:pointer;">
        + Add Product
    </button>

    <!-- Products Table -->
    <table cellpadding="10" cellspacing="0"
           style="width:100%; margin-top:20px; border-collapse:collapse; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <tr style="background:#800000; color:#fff; text-align:left;">
            <th>Image</th>
            <th>Name</th>
            <th>Price (₱)</th>
            <th>Stock</th>
            <th style="width:150px;">Actions</th>
        </tr>
        @forelse($products as $p)
            <tr style="border-bottom:1px solid #ddd; background:#fff;">
                <td>
                    @if($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}"
                             style="width:50px; height:50px; object-fit:cover; border-radius:8px; border:1px solid #ccc;">
                    @else
                        <span style="color:#aaa; font-size:14px;">No Image</span>
                    @endif
                </td>
                <td style="font-weight:bold; color:#333;">{{ $p->name }}</td>
                <td style="color:#444;">{{ number_format($p->price, 2) }}</td>
                <td>
                    @if($p->stock <= 5)
                        <span style="color:red; font-weight:bold;">{{ $p->stock }}</span>
                    @else
                        {{ $p->stock }}
                    @endif
                </td>
                <td>
                    <button onclick="openEditSidebar({{ $p->id }}, '{{ $p->name }}', {{ $p->price }}, {{ $p->stock }}, '{{ $p->image }}')"
                            style="padding:5px 12px; background:#FFD700; color:#800000; border:none; border-radius:5px; cursor:pointer; margin-right:5px;">
                        ✏ Edit
                    </button>
                    <form action="{{ route('products.destroy', $p->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="padding:5px 12px; background:#dc3545; color:#fff; border:none; border-radius:5px; cursor:pointer;">
                            🗑 Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#777;">
                    No products found.
                </td>
            </tr>
        @endforelse
    </table>

    <!-- Go to Sales Link -->
    <a class="button" href="{{ route('sales.index') }}"
       style="display:inline-block; margin-top:20px; padding:10px 20px; background:#800000;
              color:white; border-radius:5px; text-decoration:none;">
        📊 Go to Sales
    </a>

    <!-- Overlay -->
    <div id="overlay"
         style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
           background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()">
    </div>

    <!-- Add Sidebar -->
    <div id="addSidebar"
         style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff;
           border-left:4px solid #800000; box-shadow:-2px 0 8px rgba(0,0,0,0.3);
           padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
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
            <button type="submit"
                    style="padding:10px 20px; background:#800000; color:#fff; border:none; border-radius:5px; cursor:pointer;">
                Save
            </button>
            <button type="button" onclick="closeSidebars()"
                    style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer;">
                Cancel
            </button>
        </form>
    </div>

    <!-- Edit Sidebar -->
    <div id="editSidebar"
         style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff;
           border-left:4px solid #FFD700; box-shadow:-2px 0 8px rgba(0,0,0,0.3);
           padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
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
                <img id="editImagePreview" src="" alt="No image"
                     style="width:100px; height:100px; object-fit:cover; margin-bottom:10px; border:1px solid #ccc; border-radius:5px;">
            </div>
            <div style="margin-bottom:12px;">
                <label><b>Change Image:</b></label>
                <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
            </div>
            <button type="submit"
                    style="padding:10px 20px; background:#FFD700; color:#800000; border:none; border-radius:5px; cursor:pointer;">
                Update
            </button>
            <button type="button" onclick="closeSidebars()"
                    style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer;">
                Cancel
            </button>
        </form>
    </div>

    <!-- JS for Sidebars -->
    <script>
        function openAddSidebar() {
            document.getElementById("addSidebar").style.right = "0";
            document.getElementById("overlay").style.display = "block";
        }

        function openEditSidebar(id, name, price, stock, image) {
            document.getElementById("editSidebar").style.right = "0";
            document.getElementById("overlay").style.display = "block";

            // Fill form fields
            document.getElementById("editName").value = name;
            document.getElementById("editPrice").value = price;
            document.getElementById("editStock").value = stock;

            // Update form action
            document.getElementById("editForm").action = "/products/" + id;

            // Show current image
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
