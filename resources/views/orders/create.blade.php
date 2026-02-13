@extends('layouts.app')

@section('content')

<style>
.card-container{
    max-width:1000px;
    margin:40px auto;
    background:#fff;
    padding:30px;
    border-radius:16px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
}
.title{
    font-size:24px;
    font-weight:bold;
    margin-bottom:20px;
    color:#2c3e50;
}
input, textarea{
    padding:8px;
    border-radius:8px;
    border:1px solid #ddd;
    width:100%;
    margin-bottom:10px;
}
input:focus, textarea:focus{
    outline:none;
    border:1px solid #3498db;
}
.btn{
    padding:8px 16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:500;
}
.btn-add{ background:#3498db; color:#fff; }
.btn-save{ background:#2ecc71; color:#fff; }
.btn-danger{ background:#e74c3c; color:#fff; }
.btn-cancel{ background:#95a5a6; color:#fff; }
table{
    width:100%;
    margin-top:15px;
    border-collapse:collapse;
}
th{
    background:#f4f6f9;
}
th, td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #eee;
}
.summary{
    margin-top:20px;
    font-size:18px;
}
.low-stock{
    background:#fff3cd;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    border:1px solid #ffeeba;
    color:#856404;
}
</style>

<div class="card-container">

    <div class="title">เพิ่มคำสั่งซื้อ</div>

    @if(session('error'))
        <div style="color:red; margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
        @csrf

        {{-- ================= CUSTOMER SECTION ================= --}}
        <h4>ข้อมูลลูกค้า</h4>

        <label>ชื่อลูกค้า</label>
        <input type="text" name="customername" list="customerList" required>

        <datalist id="customerList">
            @foreach($customers as $c)
                <option value="{{ $c->customername }}">
            @endforeach
        </datalist>

        <label>เบอร์โทร</label>
        <input type="text" name="tel" placeholder="กรอกเมื่อเป็นลูกค้าใหม่">

        <label>ที่อยู่</label>
        <textarea name="address" rows="2" placeholder="กรอกเมื่อเป็นลูกค้าใหม่"></textarea>

        <hr>

        {{-- ================= PRODUCT SECTION ================= --}}
        <h4>รายการสินค้า</h4>

        <table id="productTable">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>จำนวน</th>
                    <th>ราคา</th>
                    <th>รวม</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <br>
        <button type="button" class="btn btn-add" onclick="addRow()">+ เพิ่มสินค้า</button>

        <hr>

        {{-- ================= SUMMARY ================= --}}
        <label>ส่วนลด</label>
        <input type="number" id="discount" value="0" min="0" oninput="calculateTotal()">

        <div class="summary">
            <div>ยอดรวม: <strong><span id="totalText">0</span></strong></div>
            <div>ยอดสุทธิ: <strong><span id="netText">0</span></strong></div>
        </div>

        <input type="hidden" name="total" id="total">
        <input type="hidden" name="net" id="net">
        <input type="hidden" name="products" id="products">

        <br><br>

        <button type="submit" class="btn btn-save">บันทึก</button>
        <button type="reset" class="btn btn-cancel">ยกเลิก</button>

    </form>
</div>

<datalist id="productData">
    @foreach($products as $p)
        <option value="{{ $p->productid }}">
    @endforeach
</datalist>

<script>

let productList = @json($products);

document.getElementById("orderForm").addEventListener("submit", function(e){
    e.preventDefault();
    prepareData();
    this.submit();
});

function addRow(){
    let table = document.querySelector("#productTable tbody");
    let row = table.insertRow();

    row.innerHTML = `
        <td>
            <input type="text" list="productData"
                   oninput="updatePriceByProdId(this)"
                   placeholder="กรอก Product ID">
        </td>
        <td>
            <input type="number" value="1" min="1"
                   oninput="calculateRow(this)">
        </td>
        <td class="price">0</td>
        <td class="subtotal">0</td>
        <td>
            <button type="button" class="btn btn-danger"
            onclick="this.closest('tr').remove(); calculateTotal();">
            ลบ
            </button>
        </td>
    `;
}

function updatePriceByProdId(input){
    let prod = productList.find(p => p.productid == input.value);
    let row = input.closest("tr");

    row.querySelector(".price").innerText = prod ? prod.price : 0;
    calculateRow(input);
}

function calculateRow(el){
    let row = el.closest("tr");
    let qty = Number(row.querySelector("input[type='number']").value);
    let price = Number(row.querySelector(".price").innerText);

    let subtotal = qty * price;
    row.querySelector(".subtotal").innerText = subtotal;

    calculateTotal();
}

function calculateTotal(){
    let total = 0;

    document.querySelectorAll(".subtotal").forEach(td=>{
        total += Number(td.innerText);
    });

    let discount = Number(document.getElementById("discount").value);
    let net = total - discount;
    if(net < 0) net = 0;

    document.getElementById("totalText").innerText = total;
    document.getElementById("netText").innerText = net;

    document.getElementById("total").value = total;
    document.getElementById("net").value = net;
}

function prepareData(){
    let data = [];

    document.querySelectorAll("#productTable tbody tr").forEach(row=>{
        let productid = row.querySelector("input[list='productData']").value;
        let quantity = row.querySelector("input[type='number']").value;
        let price = row.querySelector(".price").innerText;
        let subtotal = row.querySelector(".subtotal").innerText;

        if(productid){
            data.push({
                productid: productid,
                quantity: quantity,
                price: price,
                subtotal: subtotal
            });
        }
    });

    document.getElementById("products").value = JSON.stringify(data);
}

</script>

@endsection
