<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Furniture System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        /* ===== Hamburger ===== */
        .hamburger {
            position: fixed;
            top: 20px;
            right: 25px;
            font-size: 22px;
            cursor: pointer;
            z-index: 1100;
        }

        /* ===== Overlay ===== */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            display: none;
            z-index: 1000;
            pointer-events: none;
        }

        .overlay.active {
            display: block;
            pointer-events: auto;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            position: fixed;
            top: 0;
            right: -260px;
            width: 260px;
            height: 100%;
            background: #fff;
            box-shadow: -2px 0 12px rgba(0,0,0,0.1);
            padding-top: 20px;
            transition: 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }

        .sidebar.active { right: 0; }

        .sidebar p {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .sidebar a,
        .menu-item {
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .sidebar a:hover,
        .menu-item:hover {
            background: #f2f2f2;
        }

        /* ===== Active link ===== */
        .sidebar a.active-link {
            background: #e9ecef;
            font-weight: bold;
            color: #000;
        }

        .submenu {
            display: none;
            background: #fafafa;
        }

        .submenu a {
            padding-left: 35px;
            font-size: 14px;
        }

        /* ===== Main Content ===== */
        .main-content {
            padding: 100px 20px 40px 20px;
        }
    </style>
</head>
<body>

<div class="hamburger" onclick="openSidebar()">☰</div>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
    <p>Wood Furniture</p>

    <a href="/dashboard"
       class="{{ request()->is('dashboard') ? 'active-link' : '' }}">
       Dashboard
    </a>

    <div class="menu-item" onclick="toggleMenu('orderMenu')">
        คำสั่งซื้อ ▼
    </div>
    <div class="submenu" id="orderMenu">
        <a href="/orders"
           class="{{ request()->is('orders') ? 'active-link' : '' }}">
           รายการคำสั่งซื้อ
        </a>
        <a href="/orders/create"
           class="{{ request()->is('orders/create') ? 'active-link' : '' }}">
           เพิ่มคำสั่งซื้อ
        </a>
    </div>

    <div class="menu-item" onclick="toggleMenu('productMenu')">
        สินค้า ▼
    </div>
    <div class="submenu" id="productMenu">
        <a href="/products"
           class="{{ request()->is('products') ? 'active-link' : '' }}">
           รายการสินค้า
        </a>
        <a href="/products/create"
           class="{{ request()->is('products/create') ? 'active-link' : '' }}">
           เพิ่มสินค้า
        </a>
    </div>

    <a href="/suppliers"
       class="{{ request()->is('suppliers') ? 'active-link' : '' }}">
       Supplier
    </a>

    <a href="/sales"
       class="{{ request()->is('sales') ? 'active-link' : '' }}">
       สรุปยอดขาย
    </a>

    <a href="/profile"
       class="{{ request()->is('profile') ? 'active-link' : '' }}">
       Profile
    </a>
</div>

<div class="main-content">
    @yield('content')
</div>

<script>
function openSidebar() {
    document.getElementById("sidebar").classList.add("active");
    document.getElementById("overlay").classList.add("active");
}

function closeSidebar() {
    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("active");
}

function toggleMenu(id) {
    const menu = document.getElementById(id);

    document.querySelectorAll('.submenu').forEach(sub => {
        if (sub !== menu) {
            sub.style.display = "none";
        }
    });

    menu.style.display =
        menu.style.display === "block" ? "none" : "block";
}

/* ===== Auto open dropdown when inside section ===== */
window.addEventListener("DOMContentLoaded", function() {

    if (window.location.pathname.startsWith("/orders")) {
        document.getElementById("orderMenu").style.display = "block";
    }

    if (window.location.pathname.startsWith("/products")) {
        document.getElementById("productMenu").style.display = "block";
    }

});
</script>

</body>
</html>
