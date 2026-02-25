<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Furniture System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* ===============================
   GLOBAL RESET
=================================*/
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Arial, sans-serif;
            scroll-behavior: smooth;
        }

        /* ===============================
   FULL BACKGROUND THEME
=================================*/
        body {
            background: linear-gradient(135deg, #f3ebe2, #e8dccd);
            min-height: 100vh;
            color: #4e342e;
        }

        /* ===============================
   HAMBURGER BUTTON
=================================*/
        .hamburger {
            position: fixed;
            top: 20px;
            right: 25px;
            width: 32px;
            height: 24px;
            cursor: pointer;
            z-index: 1100;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hamburger span {
            height: 3px;
            width: 100%;
            background: #5a3e2b;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Animation when active */
        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* ===============================
   OVERLAY
=================================*/
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            z-index: 1000;
            backdrop-filter: blur(3px);
        }

        .overlay.active {
            display: block;
        }

        /* ===============================
   SIDEBAR
=================================*/
        .sidebar {
            position: fixed;
            top: 0;
            right: -280px;
            width: 280px;
            height: 100%;
            background: linear-gradient(180deg, #ffffff, #f3ebe2);
            box-shadow: -5px 0 30px rgba(93, 64, 55, 0.25);
            padding-top: 25px;
            transition: right 0.35s ease;
            z-index: 1050;
            overflow-y: auto;
            border-left: 1px solid rgba(93, 64, 55, 0.15);
        }

        .sidebar.active {
            right: 0;
        }

        .sidebar p {
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 18px;
            color: #4e342e;
            letter-spacing: 1px;
        }

        /* ===============================
   SIDEBAR LINKS
=================================*/
        .sidebar a,
        .menu-item {
            display: block;
            padding: 14px 22px;
            text-decoration: none;
            color: #4e342e;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.25s ease;
            font-weight: 500;
        }

        .sidebar a:hover,
        .menu-item:hover {
            background: rgba(93, 64, 55, 0.08);
            padding-left: 28px;
        }

        .sidebar a.active-link {
            background: rgba(93, 64, 55, 0.15);
            font-weight: 700;
            color: #3e2723;
        }

        /* ===============================
   SUBMENU
=================================*/
        .submenu {
            display: none;
            background: #faf7f3;
        }

        .submenu a {
            padding-left: 40px;
            font-size: 14px;
        }

        /* ===============================
   MAIN CONTENT
=================================*/
        .main-content {
            padding: 40px;
            min-height: 100vh;
            transition: filter 0.3s ease;
        }

        /* ===============================
   SCROLLBAR STYLE (Optional)
=================================*/
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(93, 64, 55, 0.3);
            border-radius: 10px;
        }

        /* ===============================
   RESPONSIVE
=================================*/
        @media (max-width: 768px) {

            .sidebar {
                width: 240px;
            }

            .main-content {
                padding: 20px;
            }

            .hamburger {
                right: 15px;
            }
        }
    </style>
</head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<body>

    <div class="hamburger" onclick="toggleSidebar(this)">
        <span></span>
        <span></span>
        <span></span>
    </div>


    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <p>Wood Furniture</p>

        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active-link' : '' }}">
            หน้าหลัก
        </a>

        <div class="menu-item" onclick="toggleMenu('orderMenu')">
            คำสั่งซื้อ ▼
        </div>
        <div class="submenu" id="orderMenu">
            <a href="/orders" class="{{ request()->is('orders') ? 'active-link' : '' }}">
                รายการคำสั่งซื้อ
            </a>
            <a href="/orders/create" class="{{ request()->is('orders/create') ? 'active-link' : '' }}">
                เพิ่มคำสั่งซื้อ
            </a>
        </div>

        <div class="menu-item" onclick="toggleMenu('productMenu')">
            สินค้า ▼
        </div>
        <div class="submenu" id="productMenu">
            <a href="/products" class="{{ request()->is('products') ? 'active-link' : '' }}">
                รายการสินค้า
            </a>
            <a href="/products/create" class="{{ request()->is('products/create') ? 'active-link' : '' }}">
                เพิ่มสินค้า
            </a>
        </div>

        <a href="/suppliers" class="{{ request()->is('suppliers') ? 'active-link' : '' }}">
            ซัพพลายเออร์
        </a>

        @if(session('role') === 'เจ้าของ')
            <a href="{{ route('sales.summary') }}" class="{{ request()->is('sales-summary') ? 'active-link' : '' }}">
                สรุปยอดขาย
            </a>

            <a href="/profile" class="{{ request()->is('profile') ? 'active-link' : '' }}">
                พนักงาน
            </a>
        @endif


    </div>

    <div class="main-content">
        @yield('content')

    </div>

    <script>

        function toggleSidebar(button) {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("overlay");

            sidebar.classList.toggle("active");
            overlay.classList.toggle("active");
            button.classList.toggle("active");
        }

        function closeSidebar() {
            document.getElementById("sidebar").classList.remove("active");
            document.getElementById("overlay").classList.remove("active");
            document.querySelector(".hamburger").classList.remove("active");
        }

        function toggleMenu(id) {
            const menu = document.getElementById(id);

            document.querySelectorAll('.submenu').forEach(sub => {
                if (sub !== menu) sub.style.display = "none";
            });

            menu.style.display =
                menu.style.display === "block" ? "none" : "block";
        }

        window.addEventListener("DOMContentLoaded", function () {

            if (window.location.pathname.startsWith("/orders")) {
                document.getElementById("orderMenu").style.display = "block";
            }

            if (window.location.pathname.startsWith("/products")) {
                document.getElementById("productMenu").style.display = "block";
            }
        });
    </script>


    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>