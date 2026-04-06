<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - E-Commerce</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== Sidebar Scrollbar ===== */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 4px; }

        /* ===== Submenu Animation ===== */
        .nav-submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .nav-submenu.open { max-height: 500px; }

        /* ===== Submenu Dot Indicator ===== */
        .nav-submenu a { position: relative; }
        .nav-submenu a::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: rgba(148,163,184,0.3);
            position: absolute;
            left: 34px;
            top: 50%;
            transform: translateY(-50%);
        }
        .nav-submenu a:hover::before { background: #CBD5E1; }
        .nav-submenu a.sub-active::before { background: #F97316; box-shadow: 0 0 6px rgba(249,115,22,0.4); }

        /* ===== Chevron rotation ===== */
        .nav-chevron { transition: transform 0.25s ease; }
        .nav-chevron.rotated { transform: rotate(180deg); }
    </style>
</head>
<body style="margin:0; padding:0; background:#F9FAFB;">

    <div style="display:flex; min-height:100vh;">

        {{-- ============ SIDEBAR ============ --}}
        <aside id="admin-sidebar" style="width:260px; min-width:260px; background:linear-gradient(180deg,#0F1A2E 0%,#162035 50%,#1A2744 100%); display:flex; flex-direction:column; height:100vh; position:sticky; top:0; overflow:hidden;">

            {{-- Logo --}}
            <div style="padding:20px 20px 16px; border-bottom:1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
                    <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#F97316,#EA580C); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-store" style="color:white; font-size:14px;"></i>
                    </div>
                    <div>
                        <span style="font-size:20px; font-weight:700; background:linear-gradient(135deg,#F97316,#FB923C); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">ShopBD</span>
                        <span style="font-size:10px; font-weight:600; background:rgba(249,115,22,0.15); color:#FB923C; padding:2px 8px; border-radius:4px; margin-left:6px; letter-spacing:0.5px; text-transform:uppercase;">Admin</span>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="sidebar-nav" style="flex:1; overflow-y:auto; overflow-x:hidden; padding:8px 0;">

                {{-- MAIN --}}
                <div style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(148,163,184,0.5); padding:14px 20px 6px;">Main</div>

                <a href="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.dashboard') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; text-decoration:none; border-left:3px solid {{ request()->routeIs('admin.dashboard') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.dashboard') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                    <i class="fas fa-th-large" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Inventory --}}
                <div>
                    <div onclick="toggleDropdown('inventory')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.inventory.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.inventory.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.inventory.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-warehouse" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Inventory</span>
                        <i class="fas fa-chevron-down nav-chevron" id="inventory-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="inventory-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.inventory.dashboard') }}" class="{{ request()->routeIs('admin.inventory.dashboard') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.inventory.dashboard') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Dashboard</a>
                        <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.index') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.inventory.index') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Manage Stock</a>
                        <a href="{{ route('admin.inventory.bulk-update') }}" class="{{ request()->routeIs('admin.inventory.bulk-update') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.inventory.bulk-update') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Bulk Update</a>
                        <a href="{{ route('admin.inventory.alerts') }}" class="{{ request()->routeIs('admin.inventory.alerts') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.inventory.alerts') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Stock Alerts</a>
                        <a href="{{ route('admin.inventory.reports') }}" class="{{ request()->routeIs('admin.inventory.reports') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.inventory.reports') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Reports</a>
                    </div>
                </div>

                {{-- CATALOG --}}
                <div style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(148,163,184,0.5); padding:14px 20px 6px;">Catalog</div>

                {{-- Products --}}
                <div>
                    <div onclick="toggleDropdown('products')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-cube" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Products</span>
                        <i class="fas fa-chevron-down nav-chevron" id="products-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="products-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.products.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">All Products</a>
                        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.categories.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Categories</a>
                        <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.brands.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Brands</a>
                    </div>
                </div>

                {{-- SALES --}}
                <div style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(148,163,184,0.5); padding:14px 20px 6px;">Sales</div>

                {{-- Orders --}}
                <div>
                    <div onclick="toggleDropdown('orders')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.tracking.*') || request()->routeIs('admin.courier-integrations.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.tracking.*') || request()->routeIs('admin.courier-integrations.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.tracking.*') || request()->routeIs('admin.courier-integrations.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-shopping-bag" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Orders</span>
                        <i class="fas fa-chevron-down nav-chevron" id="orders-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="orders-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.orders.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">All Orders</a>
                        <a href="{{ route('admin.tracking.index') }}" class="{{ request()->routeIs('admin.tracking.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.tracking.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Order Tracking</a>
                        <a href="{{ route('admin.courier-integrations.index') }}" class="{{ request()->routeIs('admin.courier-integrations.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.courier-integrations.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Courier Integrations</a>
                    </div>
                </div>

                {{-- Merchants --}}
                <div>
                    <div onclick="toggleDropdown('merchants')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.merchants.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.merchants.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.merchants.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-store" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Merchants</span>
                        <i class="fas fa-chevron-down nav-chevron" id="merchants-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="merchants-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.merchants.index') }}" class="{{ request()->routeIs('admin.merchants.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.merchants.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">All Merchants</a>
                    </div>
                </div>

                {{-- MARKETING --}}
                <div style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(148,163,184,0.5); padding:14px 20px 6px;">Marketing</div>

                {{-- Promotions --}}
                <div>
                    <div onclick="toggleDropdown('marketing')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.coupons.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.coupons.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.coupons.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-bullhorn" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Promotions</span>
                        <i class="fas fa-chevron-down nav-chevron" id="marketing-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="marketing-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.banners.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Banners</a>
                        <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.coupons.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Coupons</a>
                    </div>
                </div>

                {{-- Content --}}
                <div>
                    <div onclick="toggleDropdown('content')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.reviews.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.reviews.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.reviews.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-comment-dots" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Content</span>
                        <i class="fas fa-chevron-down nav-chevron" id="content-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="content-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.reviews.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Reviews</a>
                    </div>
                </div>

                {{-- CONFIGURATION --}}
                <div style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(148,163,184,0.5); padding:14px 20px 6px;">Configuration</div>

                {{-- System --}}
                <div>
                    <div onclick="toggleDropdown('system')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.commissions.*') || request()->routeIs('admin.payment-settings.*') || request()->routeIs('admin.branding.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.shipping.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.commissions.*') || request()->routeIs('admin.payment-settings.*') || request()->routeIs('admin.branding.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.shipping.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.commissions.*') || request()->routeIs('admin.payment-settings.*') || request()->routeIs('admin.branding.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.shipping.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-sliders-h" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>System</span>
                        <i class="fas fa-chevron-down nav-chevron" id="system-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="system-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.commissions.index') }}" class="{{ request()->routeIs('admin.commissions.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.commissions.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Commission</a>
                        <a href="{{ route('admin.payment-settings.index') }}" class="{{ request()->routeIs('admin.payment-settings.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.payment-settings.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Payment</a>
                        <a href="{{ route('admin.shipping.index') }}" class="{{ request()->routeIs('admin.shipping.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.shipping.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Shipping</a>
                        <a href="{{ route('admin.branding.index') }}" class="{{ request()->routeIs('admin.branding.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.branding.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Branding</a>
                        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.settings.*') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">General Settings</a>
                    </div>
                </div>

                {{-- Support --}}
                <div>
                    <div onclick="toggleDropdown('support')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.support.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.support.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.support.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-life-ring" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Support</span>
                        <i class="fas fa-chevron-down nav-chevron" id="support-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="support-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.support.dashboard') }}" class="{{ request()->routeIs('admin.support.dashboard') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.support.dashboard') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Dashboard</a>
                        <a href="{{ route('admin.support.index') }}" class="{{ request()->routeIs('admin.support.index') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.support.index') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">All Tickets</a>
                    </div>
                </div>

                {{-- SECURITY --}}
                <div style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(148,163,184,0.5); padding:14px 20px 6px;">Security</div>

                {{-- Suspicious Customers --}}
                <div>
                    <div onclick="toggleDropdown('suspicious-customers')" style="display:flex; align-items:center; padding:10px 20px; color:{{ request()->routeIs('admin.suspicious-customers.*') ? '#F97316' : 'rgba(203,213,225,0.85)' }}; font-size:13.5px; font-weight:500; cursor:pointer; border-left:3px solid {{ request()->routeIs('admin.suspicious-customers.*') ? '#F97316' : 'transparent' }}; background:{{ request()->routeIs('admin.suspicious-customers.*') ? 'rgba(249,115,22,0.08)' : 'transparent' }};">
                        <i class="fas fa-shield-alt" style="width:20px; text-align:center; margin-right:12px; font-size:15px; opacity:0.8;"></i>
                        <span>Suspicious Customers</span>
                        <i class="fas fa-chevron-down nav-chevron" id="suspicious-customers-chevron" style="margin-left:auto; font-size:10px; opacity:0.4;"></i>
                    </div>
                    <div id="suspicious-customers-menu" class="nav-submenu" style="background:rgba(0,0,0,0.12);">
                        <a href="{{ route('admin.suspicious-customers.index') }}" class="{{ request()->routeIs('admin.suspicious-customers.index') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.suspicious-customers.index') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">All Suspicious</a>
                        <a href="{{ route('admin.suspicious-customers.create') }}" class="{{ request()->routeIs('admin.suspicious-customers.create') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.suspicious-customers.create') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Add Customer</a>
                        <a href="{{ route('admin.suspicious-customers.analytics') }}" class="{{ request()->routeIs('admin.suspicious-customers.analytics') ? 'sub-active' : '' }}" style="display:flex; align-items:center; padding:9px 20px 9px 52px; color:{{ request()->routeIs('admin.suspicious-customers.analytics') ? '#F97316' : 'rgba(148,163,184,0.8)' }}; font-size:13px; text-decoration:none;">Analytics</a>
                    </div>
                </div>

                <div style="height:20px;"></div>
            </nav>

            {{-- Footer / Profile --}}
            <div style="border-top:1px solid rgba(255,255,255,0.06); padding:14px 16px;">
                <div style="display:flex; align-items:center; padding:8px; border-radius:10px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#F97316,#EA580C); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:white; flex-shrink:0;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div style="margin-left:10px; overflow:hidden; flex:1;">
                        <div style="font-size:13px; font-weight:600; color:#E2E8F0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
                        <div style="font-size:11px; color:rgba(148,163,184,0.6);">Administrator</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin-left:auto;">
                        @csrf
                        <button type="submit" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; color:#94A3B8; background:transparent; border:none; cursor:pointer;" title="Logout">
                            <i class="fas fa-sign-out-alt" style="font-size:13px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ============ MAIN CONTENT ============ --}}
        <div style="flex:1; min-width:0; display:flex; flex-direction:column;">

            {{-- Header --}}
            <header style="background:white; border-bottom:1px solid #E5E7EB; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:30;">
                <h2 style="font-size:18px; font-weight:600; color:#1F2937; margin:0;">@yield('header', 'Dashboard')</h2>
                <div style="display:flex; align-items:center; gap:16px;">
                    <a href="{{ route('home') }}" target="_blank" style="font-size:13px; color:#6B7280; text-decoration:none; display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-external-link-alt" style="font-size:11px;"></i>
                        <span>View Store</span>
                    </a>
                    <div style="width:1px; height:20px; background:#E5E7EB;"></div>
                    <span style="font-size:13px; color:#6B7280;">{{ Auth::user()->name }}</span>
                </div>
            </header>

            {{-- Page Content --}}
            <main style="flex:1; padding:24px;">
                @if(session('success'))
                    <div id="success-message" style="background:linear-gradient(to right,#22C55E,#16A34A); color:white; padding:16px 24px; border-radius:8px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center;">
                            <div style="background:rgba(255,255,255,0.2); border-radius:50%; padding:12px; margin-right:16px;">
                                <i class="fas fa-check-circle" style="font-size:24px;"></i>
                            </div>
                            <div>
                                <p style="font-weight:600; font-size:16px; margin:0;">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" style="color:white; background:none; border:none; cursor:pointer; font-size:18px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const msg = document.getElementById('success-message');
                            if (msg) setTimeout(() => { msg.style.opacity='0'; msg.style.transition='opacity 0.5s'; setTimeout(() => msg.remove(), 500); }, 8000);
                        });
                    </script>
                @endif

                @if(session('error'))
                    <div id="error-message" style="background:linear-gradient(to right,#EF4444,#DC2626); color:white; padding:16px 24px; border-radius:8px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center;">
                            <div style="background:rgba(255,255,255,0.2); border-radius:50%; padding:12px; margin-right:16px;">
                                <i class="fas fa-exclamation-circle" style="font-size:24px;"></i>
                            </div>
                            <div>
                                <p style="font-weight:600; font-size:16px; margin:0;">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" style="color:white; background:none; border:none; cursor:pointer; font-size:18px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const msg = document.getElementById('error-message');
                            if (msg) setTimeout(() => { msg.style.opacity='0'; msg.style.transition='opacity 0.5s'; setTimeout(() => msg.remove(), 500); }, 10000);
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
    function toggleDropdown(menuId) {
        const menu = document.getElementById(menuId + '-menu');
        const chevron = document.getElementById(menuId + '-chevron');

        const allMenus = ['inventory', 'products', 'orders', 'merchants', 'marketing', 'content', 'system', 'support', 'suspicious-customers'];
        allMenus.forEach(id => {
            if (id !== menuId) {
                const m = document.getElementById(id + '-menu');
                const c = document.getElementById(id + '-chevron');
                if (m && m.classList.contains('open')) { m.classList.remove('open'); if (c) c.classList.remove('rotated'); }
            }
        });

        if (menu.classList.contains('open')) {
            menu.classList.remove('open');
            chevron.classList.remove('rotated');
        } else {
            menu.classList.add('open');
            chevron.classList.add('rotated');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const p = window.location.pathname;
        if (p.includes('/inventory')) toggleDropdown('inventory');
        else if (p.includes('/products') || p.includes('/categories') || p.includes('/brands')) toggleDropdown('products');
        else if (p.includes('/orders') || p.includes('/tracking') || p.includes('/courier-integrations')) toggleDropdown('orders');
        else if (p.includes('/merchants')) toggleDropdown('merchants');
        else if (p.includes('/banners') || p.includes('/coupons')) toggleDropdown('marketing');
        else if (p.includes('/reviews')) toggleDropdown('content');
        else if (p.includes('/commissions') || p.includes('/payment-settings') || p.includes('/branding') || p.includes('/settings') || p.includes('/shipping')) toggleDropdown('system');
        else if (p.includes('/support')) toggleDropdown('support');
        else if (p.includes('/suspicious-customers')) toggleDropdown('suspicious-customers');
    });
    </script>
</body>
</html>
