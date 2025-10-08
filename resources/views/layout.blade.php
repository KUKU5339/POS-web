<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'StreetPOS') }}</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFD700">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="StreetPOS">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
        }

        /* Top Header */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #800000, #a00000);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .hamburger {
            font-size: 24px;
            cursor: pointer;
            display: none;
            color: #FFD700;
            transition: transform 0.3s;
        }

        .hamburger:hover {
            transform: scale(1.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #FFD700;
        }

        .logo i {
            font-size: 28px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #FFD700;
            font-weight: 600;
        }

        .user-name i {
            font-size: 18px;
        }

        .icon-btn {
            background: none;
            border: none;
            color: #FFD700;
            font-size: 20px;
            cursor: pointer;
            position: relative;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .icon-btn:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #dc3545;
            color: #fff;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Dropdown */
        .dropdown-box {
            display: none;
            position: absolute;
            top: 55px;
            right: 0;
            background: #fff;
            color: #333;
            min-width: 280px;
            max-width: 320px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 2000;
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-box.active {
            display: block;
        }

        .dropdown-header {
            padding: 15px;
            background: #800000;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-content {
            max-height: 300px;
            overflow-y: auto;
        }

        .dropdown-content::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dropdown-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            transition: background 0.2s;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.critical {
            background: #fff5f5;
            border-left: 3px solid #dc3545;
        }

        .notification-item.warning {
            background: #fffbf0;
            border-left: 3px solid #ffc107;
        }

        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #999;
        }

        .notification-empty i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 10px;
        }

        .dropdown-footer {
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
        }

        .dropdown-footer a {
            color: #800000;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: block;
            text-align: center;
        }

        .dropdown-footer a:hover {
            color: #a00000;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 999;
        }

        .sidebar a {
            padding: 14px 20px;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover {
            background: #f8f9fa;
            border-left-color: #FFD700;
            color: #800000;
        }

        .sidebar a.active {
            background: #fff3cd;
            border-left-color: #800000;
            color: #800000;
            font-weight: 600;
        }

        .sidebar a i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .sidebar .logout {
            margin-top: auto;
            border-top: 1px solid #eee;
            color: #dc3545;
        }

        .sidebar .logout:hover {
            background: #fff5f5;
            border-left-color: #dc3545;
        }

        /* Content */
        .content {
            margin-left: 240px;
            margin-top: 60px;
            padding: 30px;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.3s ease;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .hamburger {
                display: block;
            }

            .content {
                margin-left: 0;
                padding: 20px 15px;
            }

            .user-name span {
                display: none;
            }

            .header-right {
                gap: 10px;
            }

            .logo span {
                display: none;
            }

            .dropdown-box {
                right: -10px;
                min-width: 260px;
            }
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }

        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <i class="hamburger fas fa-bars" onclick="toggleSidebar()"></i>
            <div class="logo">
                <i class="fas fa-store"></i>
                <span>StreetPOS</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-name">
                <i class="fa fa-user-circle"></i>
                <span>{{ Auth::user()->name ?? 'Guest' }}</span>
            </div>

            <!-- Notifications -->
            @php
            $user = Auth::user();
            $threshold = $user->default_stock_threshold ?? 5;
            $lowStockProducts = \App\Models\Product::where('user_id', $user->id)
            ->where('stock', '<=', $threshold)
                ->get();
                $lowStockCount = $lowStockProducts->count();
                @endphp

                <div class="notification-wrapper" style="position: relative;">
                    <button class="icon-btn" title="Low Stock Notifications" onclick="toggleNotifications()">
                        <i class="fa fa-bell"></i>
                        @if($lowStockCount > 0)
                        <span class="notification-badge">{{ $lowStockCount }}</span>
                        @endif
                    </button>

                    <div class="dropdown-box" id="notificationDropdown">
                        <div class="dropdown-header">
                            <i class="fas fa-bell"></i>
                            Low Stock Alerts
                        </div>
                        <div class="dropdown-content">
                            @if($lowStockCount > 0)
                            @foreach($lowStockProducts as $product)
                            <div class="notification-item {{ $product->stock == 0 ? 'critical' : 'warning' }}">
                                <strong>{{ $product->name }}</strong><br>
                                <small style="color:#666;">
                                    @if($product->stock == 0)
                                    🚨 Out of stock
                                    @else
                                    ⚠️ Only {{ $product->stock }} left
                                    @endif
                                </small>
                            </div>
                            @endforeach
                            @else
                            <div class="notification-empty">
                                <i class="fas fa-check-circle" style="color:#28a745;"></i>
                                <p>All stocked up! 🎉</p>
                            </div>
                            @endif
                        </div>
                        @if($lowStockCount > 0)
                        <div class="dropdown-footer">
                            <a href="{{ route('stock-alerts.index') }}">View All Alerts →</a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- QR Code -->
                <div class="qr-wrapper" style="position: relative;">
                    <button class="icon-btn" title="App QR Code" onclick="toggleQR()">
                        <i class="fa fa-qrcode"></i>
                    </button>

                    <div class="dropdown-box" id="qrDropdown">
                        <div class="dropdown-header">
                            <i class="fas fa-qrcode"></i>
                            Scan to Access
                        </div>
                        <div style="text-align:center; padding:20px; background:#fff;">
                            {!! QrCode::size(180)->color(128,0,0)->backgroundColor(255,255,255)->generate(url('http://192.168.0.106:8000')) !!}
                            <p style="margin-top:10px; font-size:12px; color:#666;">
                                Scan with your phone
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="icon-btn" title="Logout">
                        <i class="fa fa-sign-out-alt"></i>
                    </button>
                </form>
        </div>
    </header>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="{{ route('sales.quick') }}" class="{{ request()->routeIs('sales.quick') ? 'active' : '' }}">
            <i class="fas fa-bolt"></i> Quick Sale
        </a>
        <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Sales History
        </a>
        <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="fas fa-calculator"></i> Profit Calculator
        </a>
        <a href="{{ route('stock-alerts.index') }}" class="{{ request()->routeIs('stock-alerts.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Stock Alerts
        </a>
        <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </nav>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function toggleNotifications() {
            const notifDropdown = document.getElementById('notificationDropdown');
            const qrDropdown = document.getElementById('qrDropdown');

            notifDropdown.classList.toggle('active');
            qrDropdown.classList.remove('active');
        }

        function toggleQR() {
            const qrDropdown = document.getElementById('qrDropdown');
            const notifDropdown = document.getElementById('notificationDropdown');

            qrDropdown.classList.toggle('active');
            notifDropdown.classList.remove('active');
        }

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.notification-wrapper') && !event.target.closest('.qr-wrapper')) {
                document.getElementById('notificationDropdown').classList.remove('active');
                document.getElementById('qrDropdown').classList.remove('active');
            }
        }

        // Close sidebar when clicking on a link (mobile)
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    toggleSidebar();
                });
            });
        }
    </script>
</body>

</html>