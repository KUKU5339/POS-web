<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'StreetPOS') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; }

        /* Top Header */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #800000;
            color: #ffcc00;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .header-left { display: flex; align-items: center; gap: 10px; }
        .header-left h1 { margin: 0; font-size: 24px; }
        .hamburger { font-size: 24px; cursor: pointer; display: none; }
        .header-right { display: flex; align-items: center; gap: 15px; font-weight: bold; position: relative; }
        .icon-btn { background: none; border: none; color: #ffcc00; font-size: 20px; cursor: pointer; position: relative; }
        .icon-btn:hover { color: #fff; }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: #fff;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        /* Dropdown Shared */
        .dropdown-box {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background: #fff;
            color: #333;
            min-width: 250px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            border-radius: 5px;
            z-index: 2000;
        }
        .dropdown-box.active { display: block; }
        .dropdown-box h4 {
            margin: 0;
            padding: 10px;
            background: #800000;
            color: #fff;
            font-size: 14px;
            border-radius: 5px 5px 0 0;
        }
        .dropdown-box ul {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
        }
        .dropdown-box ul li {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .dropdown-box ul li:last-child { border-bottom: none; }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #800000;
            color: #fff;
            padding-top: 20px;
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease-in-out;
        }
        .sidebar a { padding: 12px 20px; color: #fff; text-decoration: none; display: block; }
        .sidebar a:hover { background: #a00000; }
        .sidebar .logout { margin-top: auto; }

        /* Content */
        .content {
            margin-left: 220px;
            margin-top: 60px;
            padding: 20px;
            background: #f5f5f5;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.3s ease-in-out;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); top: 60px; z-index: 999; }
            .sidebar.active { transform: translateX(0); }
            .hamburger { display: block; }
            .content { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Top Header -->
<header class="top-header">
    <div class="header-left">
        <span class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></span>
        <h1>StreetPOS</h1>
    </div>
    <div class="header-right">
        <span class="admin-name">
            <i class="fa fa-user-circle"></i>
            Hi, {{ Auth::user()->name ?? 'Guest' }}
        </span>

        <!-- Notifications -->
        @php
            $lowStockProducts = \App\Models\Product::where('stock', '<=', 5)->get();
            $lowStockCount = $lowStockProducts->count();
        @endphp
        <div class="notification-wrapper" style="position: relative;">
            <button class="icon-btn" title="Low Stock Notifications" onclick="toggleNotifications()">
                <i class="fa fa-bell"></i>
                @if($lowStockCount > 0)
                    <span class="notification-badge">{{ $lowStockCount }}</span>
                @endif
            </button>

            <!-- Dropdown -->
            <div class="dropdown-box" id="notificationDropdown">
                <h4>Low Stock Alerts</h4>
                <ul>
                    @if($lowStockCount > 0)
                        @foreach($lowStockProducts as $product)
                            <li>{{ $product->name }} - Only {{ $product->stock }} left</li>
                        @endforeach
                    @else
                        <li>No low stock items 🎉</li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- QR Code Button -->
        <div class="qr-wrapper" style="position: relative;">
            <button class="icon-btn" title="App QR Code" onclick="toggleQR()">
                <i class="fa fa-qrcode"></i>
            </button>

            <!-- Dropdown with QR Code -->
            <div class="dropdown-box" id="qrDropdown">
                <h4>Scan to Access</h4>
                <div style="text-align:center; padding:10px;">
                    {!! QrCode::size(200)->color(128,0,0)->backgroundColor(255,255,255)->generate(url('http://192.168.0.106:8000')) !!}
                </div>
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="icon-btn" title="Logout"><i class="fa fa-sign-out-alt"></i></button>
        </form>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="{{ route('products.index') }}"><i class="fas fa-box"></i> Products</a>
    <a href="{{ route('sales.index') }}"><i class="fas fa-shopping-cart"></i> Sales</a>
    <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
</div>

<!-- Main Content -->
<div class="content">
    @yield('content')
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    function toggleNotifications() {
        document.getElementById('notificationDropdown').classList.toggle('active');
        document.getElementById('qrDropdown').classList.remove('active');
    }

    function toggleQR() {
        document.getElementById('qrDropdown').classList.toggle('active');
        document.getElementById('notificationDropdown').classList.remove('active');
    }

    // Close dropdowns if clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('.notification-wrapper') && !event.target.closest('.qr-wrapper')) {
            document.getElementById('notificationDropdown').classList.remove('active');
            document.getElementById('qrDropdown').classList.remove('active');
        }
    }
</script>

</body>
</html>
