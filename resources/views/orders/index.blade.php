@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/order-index.css') }}">

<div class="container py-5">
<div class="container">

    <div class="card border-0 shadow-lg"
         style="border-radius:25px;background:#f9f5f0;">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:linear-gradient(90deg,#5a3e2b,#8b6a4f);
                    color:white;
                    border-radius:25px 25px 0 0;
                    padding:18px 25px;">
            <h5 class="mb-0 fw-bold">📦 Order Management</h5>
            <a href="{{ route('orders.create') }}"
               class="btn"
               style="background:white;color:#6b4f3a;font-weight:600;border-radius:12px;padding:6px 15px;">
                + New Order
            </a>
        </div>

        <div class="card-body p-4">

            {{-- SEARCH + STATUS --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4 position-relative">
                    <input type="text" id="liveSearch" class="form-control shadow-sm"
                           placeholder="🔍 ค้นหา Order ID หรือชื่อลูกค้า"
                           style="border-radius:12px;">
                    <div id="suggestionBox" class="list-group shadow"
                         style="position:absolute;top:45px;left:0;width:100%;
                                display:none;z-index:1000;border-radius:12px;overflow:hidden;">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ios-select-wrapper">
                        <select name="status" id="statusFilter" class="ios-select">
                            <option value="all">ทุกสถานะ</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- TABLE (desktop) --}}
            <div class="table-scroll-wrapper">
                <div class="table-scroll">
                    @include('orders.partials.table')
                </div>
            </div>

            {{-- CARD LIST (mobile) --}}
            <div class="order-card-list" id="orderCardList"></div>

        </div>
    </div>
</div>
</div>

<script>
const searchInput   = document.getElementById('liveSearch');
const statusSelect  = document.getElementById('statusFilter');
const suggestionBox = document.getElementById('suggestionBox');
const cardList      = document.getElementById('orderCardList');

let debounceTimer = null;

function badgeInfo(status) {
    if (status === 'paid')      return { color: 'bg-success', text: 'Paid' };
    if (status === 'cancelled') return { color: 'bg-danger',  text: 'Cancelled' };
    return { color: 'bg-warning text-dark', text: 'Pending' };
}

function loadOrders(showSuggestion = false) {
    const query  = searchInput.value.trim();
    const status = statusSelect.value;

    fetch(`/orders/filter?q=${query}&status=${status}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("tbody");
            tbody.innerHTML  = "";
            cardList.innerHTML = "";
            suggestionBox.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">ไม่พบข้อมูล</td>
                    </tr>`;
                cardList.innerHTML = `
                    <div class="order-card">
                        <p class="text-center text-muted mb-0">ไม่พบข้อมูล</p>
                    </div>`;
                suggestionBox.style.display = "none";
                return;
            }

            data.forEach(order => {
                const badge   = badgeInfo(order.payment_status);
                const price   = parseFloat(order.total_price ?? order.netamount).toLocaleString();
                const date    = order.created_at ?? order.orderdate ?? '-';
                const customer = order.customername ?? '-';

                // ===== SUGGESTION =====
                if (showSuggestion && query !== "") {
                    const item = document.createElement("a");
                    item.classList.add("list-group-item", "list-group-item-action");
                    item.innerHTML = `<strong>#${order.orderid}</strong> - ${customer}`;
                    item.onclick = () => {
                        searchInput.value = order.orderid;
                        suggestionBox.style.display = "none";
                        loadOrders(false);
                    };
                    suggestionBox.appendChild(item);
                }

                // ===== TABLE ROW (desktop) =====
                tbody.innerHTML += `
                <tr>
                    <td>#${order.orderid}</td>
                    <td>${customer}</td>
                    <td>${price} ฿</td>
                    <td><span class="badge ${badge.color}">${badge.text}</span></td>
                    <td>${date}</td>
                    <td>
                        <a href="/orders/${order.orderid}"
                           class="btn btn-sm"
                           style="background:#6b4f3a;color:white;border-radius:10px;">
                           👁 ดูรายละเอียด
                        </a>
                    </td>
                </tr>`;

                // ===== ORDER CARD (mobile) =====
                cardList.innerHTML += `
                <div class="order-card">
                    <div class="order-card-header">
                        <span class="order-card-id">#${order.orderid}</span>
                        <span class="badge ${badge.color}">${badge.text}</span>
                    </div>
                    <div class="order-card-body">
                        <div class="order-card-row">
                            <span class="order-card-label">ลูกค้า</span>
                            <span class="order-card-value">${customer}</span>
                        </div>
                        <div class="order-card-row">
                            <span class="order-card-label">ยอดรวม</span>
                            <span class="order-card-value">${price} ฿</span>
                        </div>
                        <div class="order-card-row">
                            <span class="order-card-label">วันที่</span>
                            <span class="order-card-value">${date}</span>
                        </div>
                    </div>
                    <div class="order-card-footer">
                        <a href="/orders/${order.orderid}" class="btn-brown">👁 ดูรายละเอียด</a>
                    </div>
                </div>`;
            });

            suggestionBox.style.display = (showSuggestion && query !== "") ? "block" : "none";
        });
}

searchInput.addEventListener("keyup", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadOrders(true), 300);
});

statusSelect.addEventListener("change", () => loadOrders(false));

document.addEventListener("click", e => {
    if (!e.target.closest(".position-relative")) {
        suggestionBox.style.display = "none";
    }
});
loadOrders(false);
</script>

@endsection