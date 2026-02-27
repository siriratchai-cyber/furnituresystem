@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="{{ asset('css/order-create.css') }}">

<div class="container mt-4">
<h3 class="mb-4 fw-semibold text-dark">🧾 สร้างคำสั่งซื้อ</h3>

<form method="POST" action="{{ route('orders.store') }}" id="orderForm">
@csrf
<input type="hidden" name="status" value="pending">

<!-- ================= CUSTOMER ================= -->
<div class="glass-card p-4 mb-4">
<div class="row">

<div class="col-md-6 mb-3">
<label>ชื่อลูกค้า</label>
<input type="text" name="customer_name" class="form-control"
placeholder="เช่น สมชาย ใจดี" required>
</div>

<div class="col-md-6 mb-3">
<label>เบอร์โทร</label>
<input type="text" name="tel" class="form-control"
placeholder="0812345678">
</div>

<div class="col-12 mb-3">
<label>ที่อยู่ลูกค้า</label>
<textarea name="customer_address"
class="form-control"
rows="2"
placeholder="99/9 หมู่ 5 ต.บางรัก อ.เมือง จ.เชียงใหม่ 50000"
required></textarea>
</div>

<div class="col-12">
<div class="form-check mt-2">
<input class="form-check-input" type="checkbox" id="needInvoice">
<label class="form-check-label">ออกใบกำกับภาษี</label>
</div>
</div>

</div>

<div class="invoice-box" id="invoiceForm">
<div class="row">
<div class="col-md-6 mb-3">
<label>ชื่อบริษัท</label>
<input type="text" name="tax_company"
class="form-control"
placeholder="บริษัท เอ บี ซี จำกัด">
</div>

<div class="col-md-6 mb-3">
<label>เลขผู้เสียภาษี</label>
<input type="text" name="tax_number"
class="form-control"
placeholder="0105551234567">
</div>
</div>
</div>
</div>

<!-- ================= PRODUCT ================= -->
<div class="glass-card p-4 mb-4">
<h5 class="mb-3 text-dark">เพิ่มสินค้า</h5>

<div class="table-scroll-wrapper">


    <div class="table-scroll" id="productScroll">
        <table class="table text-center align-middle">

<thead>
<tr>
<th width="30%">สินค้า (รหัส)</th>
<th width="10%">Stock</th>
<th width="15%">จำนวน</th>
<th width="15%">ราคา</th>
<th width="15%">รวม</th>
<th width="10%">ลบ</th>
</tr>
</thead>
<tbody id="productBody"></tbody>
         </table>
    </div>
</div>

<div class="scroll-hint">← เลื่อนเพื่อกรอกข้อมูล →</div>

<button type="button" onclick="addRow()" class="btn btn-outline-brown">
+ เพิ่มสินค้า
</button>
</div>

<!-- ================= SUMMARY ================= -->
<div class="pos-summary mb-4">
<div class="row align-items-center">
<div class="col-md-4">
<h6>ยอดรวม</h6>
<h4 id="totalText">0.00</h4>
</div>
<div class="col-md-4">
<label>ส่วนลด</label>
<input type="number" id="discount" class="form-control" value="0" min="0">
</div>
<div class="col-md-4">
<h6>ยอดสุทธิ</h6>
<h4 id="netText">0.00</h4>
</div>
</div>
</div>

<input type="hidden" name="products" id="products">
<input type="hidden" name="total" id="total">
<input type="hidden" name="netamount" id="net">
<input type="hidden" name="discount" id="discountHidden">

<div class="text-end mb-5">
<button type="button" onclick="cancelOrder()" class="btn btn-danger">
ยกเลิก
</button>
<button type="submit" class="btn btn-brown">
บันทึก
</button>
</div>

</form>
</div>

<script>

let productList=@json($products);

document.getElementById("needInvoice").addEventListener("change",function(){
document.getElementById("invoiceForm").style.display=this.checked?"block":"none";
});

function addRow(){

let row=document.createElement("tr");

row.innerHTML=`
<td>
<select class="productSelect">
<option value="">🔎 ค้นหา / เลือกสินค้า</option>
${productList.map(p=>{
let stock = parseFloat(p.stock) || 0;
let price=p.price??0;
return `<option value="${p.productid}"
data-price="${price}"
data-stock="${stock}"
${stock <= 0 ? 'disabled' : ''}>
[${p.productid}] ${p.productname} ${stock <= 0 ? '(สินค้าหมด)' : ''}
</option>`;
}).join('')}
</select>
</td>
<td class="stock">0</td>
<td><input type="number" class="form-control qty" value="1" min="1"></td>
<td class="price">0.00</td>
<td class="subtotal">0.00</td>
<td><button type="button" onclick="removeRow(this)" class="btn btn-sm btn-danger">×</button></td>
`;

document.getElementById("productBody").appendChild(row);

new TomSelect(row.querySelector(".productSelect"),{
    create:false,
    sortField:{field:"text",direction:"asc"},
    maxOptions: null,        // แสดงทั้งหมด
    openOnFocus: true,       // คลิกแล้วเปิดทันที
    allowEmptyOption: true,
    searchField: ['text'],   // ค้นหาจากข้อความทั้งหมด
        dropdownParent: 'body'
});



bindRow(row);
}

function bindRow(row){

let select=row.querySelector(".productSelect");
let qty=row.querySelector(".qty");

select.addEventListener("change",function(){

let selected=this.selectedOptions[0];
if(!selected.value) return;

let stock=parseInt(selected.dataset.stock)||0;

if(stock <= 0){
Swal.fire({
icon:"error",
title:"สินค้าหมดสต็อก"
});
select.tomselect.clear();
return;
}

let price=parseFloat(selected.dataset.price)||0;

row.dataset.price=price;
row.dataset.stock=stock;

row.querySelector(".stock").innerText=stock;
row.querySelector(".price").innerText=price.toFixed(2);

calculate();
});

qty.addEventListener("input",calculate);
}

function removeRow(btn){
btn.closest("tr").remove();
calculate();
}

function calculate(){

let total=0;

document.querySelectorAll("#productBody tr").forEach(row=>{

let price=parseFloat(row.dataset.price)||0;
let qty=parseInt(row.querySelector(".qty").value)||0;
let stock=parseInt(row.dataset.stock)||0;

if(qty<=0){
row.querySelector(".qty").value=1;
qty=1;
}

if(stock>0 && qty>stock){
row.querySelector(".qty").value=stock;
qty=stock;
}

let subtotal=price*qty;
row.querySelector(".subtotal").innerText=subtotal.toFixed(2);
total+=subtotal;
});

let discount=parseFloat(document.getElementById("discount").value)||0;
if(discount>total){
discount=total;
document.getElementById("discount").value=total;
}

let net=total-discount;

document.getElementById("totalText").innerText=total.toFixed(2);
document.getElementById("netText").innerText=net.toFixed(2);

document.getElementById("total").value=total;
document.getElementById("net").value=net;
document.getElementById("discountHidden").value=discount;
}

document.getElementById("discount").addEventListener("input",calculate);


/* ===========================
   FIXED SUBMIT FLOW
=========================== */

document.getElementById("orderForm").addEventListener("submit", function(e){

calculate();

let items=[];
let hasError=false;

document.querySelectorAll("#productBody tr").forEach(row=>{

let select=row.querySelector(".productSelect");

let productId = select.tomselect
? select.tomselect.getValue()
: select.value;

let qty=parseInt(row.querySelector(".qty").value)||0;
let price=parseFloat(row.dataset.price)||0;
let stock=parseInt(row.dataset.stock)||0;

if(!productId || qty<=0 || (stock>0 && qty>stock)){
hasError=true;
}

if(productId){
items.push({
productid:productId,
quantity:qty,
price:price
});
}

});

if(items.length===0){
e.preventDefault();
Swal.fire({
icon:"warning",
title:"ยังไม่ได้เลือกสินค้า"
});
return;
}

if(hasError){
e.preventDefault();
Swal.fire({
icon:"error",
title:"ข้อมูลไม่ถูกต้อง"
});
return;
}

// ใส่ JSON ก่อน submit
document.getElementById("products").value=JSON.stringify(items);

// ❗ ไม่ต้อง this.submit()
// ปล่อยให้ form submit ตามธรรมชาติ

});

addRow();

function cancelOrder(){
Swal.fire({
title:"ยกเลิกรายการ?",
icon:"warning",
showCancelButton:true
}).then(res=>{
if(res.isConfirmed){
window.location="{{ route('orders.index') }}";
}
});
}

</script>


@endsection
