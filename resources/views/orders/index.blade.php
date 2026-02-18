@extends('layouts.app')

@section('content')
<style>
#suggestionBox {
    animation: fadeIn 0.15s ease-in-out;
}

@keyframes fadeIn {
    from { opacity:0; transform:translateY(-5px); }
    to { opacity:1; transform:translateY(0); }
}

#suggestionBox .list-group-item:hover {
    background:#f1f1f1;
    cursor:pointer;
}
</style>

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

            <h5 class="mb-0 fw-bold">
                📦 Order Management
            </h5>

            <a href="{{ route('orders.create') }}"
               class="btn"
               style="background:white;
                      color:#6b4f3a;
                      font-weight:600;
                      border-radius:12px;
                      padding:6px 15px;">
                + New Order
            </a>
        </div>

        <div class="card-body p-4">

            {{-- SEARCH + STATUS --}}
            <div class="row g-3 mb-4">

                <div class="col-md-4 position-relative">
    <input type="text"
           id="liveSearch"
           class="form-control shadow-sm"
           placeholder="🔍 ค้นหา Order ID หรือชื่อลูกค้า"
           style="border-radius:12px;">

    <div id="suggestionBox"
         class="list-group shadow"
         style="
            position:absolute;
            top:45px;
            left:0;
            width:100%;
            display:none;
            z-index:1000;
            border-radius:12px;
            overflow:hidden;
         ">
    </div>
</div>

                <div class="col-md-3">
                   <select id="statusFilter"
        class="form-select shadow-sm"
        style="border-radius:12px;">
    <option value="">ทุกสถานะ</option>
    <option value="pending">Pending</option>
    <option value="paid">Paid</option>
    <option value="cancelled">Cancelled</option>
</select>

                </div>

            </div>

            {{-- TABLE --}}
            @include('orders.partials.table')

        </div>
    </div>
</div>
</div>

<script>
const searchInput = document.getElementById('liveSearch');
const statusSelect = document.getElementById('statusFilter');
const suggestionBox = document.getElementById('suggestionBox');

let debounceTimer = null;

function loadOrders(showSuggestion = false) {

    let query = searchInput.value.trim();
    let status = statusSelect.value;

    fetch(`/orders/filter?q=${query}&status=${status}`)
        .then(res => res.json())
        .then(data => {

            let tbody = document.querySelector("tbody");
            tbody.innerHTML = "";

            suggestionBox.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                `;
                suggestionBox.style.display = "none";
                return;
            }

            data.forEach(order => {

                // 🔥 สร้าง suggestion dropdown
                if(showSuggestion && query !== ""){

                    let item = document.createElement("a");
                    item.classList.add("list-group-item","list-group-item-action");
                    item.innerHTML = `
                        <strong>#${order.orderid}</strong> - ${order.customername ?? '-'}
                    `;

                    item.onclick = function(){
                        searchInput.value = order.orderid;
                        suggestionBox.style.display = "none";
                        loadOrders(false);
                    };

                    suggestionBox.appendChild(item);
                }

                let badgeColor = 'bg-warning text-dark';

                if(order.payment_status === 'paid'){
                    badgeColor = 'bg-success';
                }

                if(order.payment_status === 'cancelled'){
                    badgeColor = 'bg-danger';
                }

                let row = `
                <tr>
                    <td>#${order.orderid}</td>
                    <td>${order.customername ?? '-'}</td>
                    <td>${parseFloat(order.total_price ?? order.netamount).toLocaleString()} ฿</td>
                    <td>
                        <span class="badge ${badgeColor}">
                            ${order.payment_status}
                        </span>
                    </td>
                    <td>${order.created_at ?? order.orderdate ?? '-'}</td>
                    <td>
                        <a href="/orders/${order.orderid}" 
                           class="btn btn-sm"
                           style="background:#6b4f3a;color:white;border-radius:10px;">
                           👁 ดูรายละเอียดคำสั่งซื้อ
                        </a>
                    </td>
                </tr>
                `;

                tbody.innerHTML += row;
            });

            if(showSuggestion && query !== ""){
                suggestionBox.style.display = "block";
            }else{
                suggestionBox.style.display = "none";
            }

        });
}

/* 🔎 พิมพ์แล้วขึ้น dropdown */
searchInput.addEventListener("keyup", function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadOrders(true), 300);
});

/* 📂 เปลี่ยนสถานะ */
statusSelect.addEventListener("change", function(){
    loadOrders(false);
});

/* คลิกนอก dropdown ให้ปิด */
document.addEventListener("click", function(e){
    if(!e.target.closest(".position-relative")){
        suggestionBox.style.display = "none";
    }
});
</script>


@endsection
