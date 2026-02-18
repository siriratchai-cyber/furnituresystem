@extends('layouts.app')

@section('content')

@php
use Carbon\Carbon;
$thaiTime = Carbon::parse($order->orderdate)
        ->timezone('Asia/Bangkok')
        ->format('d/m/Y H:i');
@endphp

<style>
body{
    background:linear-gradient(135deg,#ece3dc,#f6efe9);
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}
.wrapper{ max-width:1300px; margin:60px auto; }
.card-container{
    background:#fff;
    border-radius:30px;
    padding:45px;
    box-shadow:0 25px 70px rgba(93,64,55,.15);
}
.pos-layout{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:45px;
}
@media(max-width:1000px){
    .pos-layout{ grid-template-columns:1fr; }
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
}
.title{ font-size:24px; font-weight:700; color:#4e342e; }
.meta{ font-size:14px; color:#8d6e63; }

.badge{
    padding:8px 20px;
    border-radius:40px;
    font-size:13px;
    font-weight:600;
}
.badge-paid{ background:#dcedc8; color:#33691e; }
.badge-pending{ background:#ffe0b2; color:#bf360c; }
.badge-cancelled{ background:#ef9a9a; color:#b71c1c; }

.section{ margin-bottom:40px; }
.section-title{
    font-weight:600;
    font-size:16px;
    margin-bottom:18px;
    color:#5d4037;
}

.info-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}
.info-item{
    background:#faf7f5;
    padding:20px;
    border-radius:18px;
    border:1px solid #eee;
}
.info-label{ font-size:13px; color:#8d6e63; }
.info-value{ font-weight:600; color:#3e2723; }

.tax-box{
    margin-top:18px;
    padding:20px;
    border-radius:18px;
    background:#f3ede9;
    font-size:14px;
}

table{ width:100%; border-collapse:collapse; }
th{
    background:#f1ece8;
    padding:14px;
    font-size:14px;
}
td{
    padding:14px;
    border-bottom:1px solid #f0e6df;
}

.summary{ text-align:right; margin-top:20px; }
.total{
    font-size:20px;
    font-weight:700;
    margin-top:10px;
}

.footer{
    display:flex;
    justify-content:space-between;
    margin-top:30px;
}
.btn{
    padding:12px 25px;
    border-radius:40px;
    border:none;
    cursor:pointer;
    text-decoration:none;
    color:white;
    font-weight:600;
}
.btn-back{ background:#8d6e63; }
.btn-receipt{ background:#5d4037; }

.payment-card{
    background:#faf7f5;
    border-radius:28px;
    padding:35px;
    box-shadow:0 20px 50px rgba(93,64,55,.1);
    display:flex;
    flex-direction:column;
    gap:25px;
}
.pay-title{
    font-size:18px;
    font-weight:700;
    color:#4e342e;
}
.qr-box{
    text-align:center;
    padding:25px;
    background:white;
    border-radius:20px;
    border:1px dashed #ddd;
}
.qr-box img{
    width:180px;
    height:180px;
    object-fit:contain;
}
.total-box{
    background:linear-gradient(135deg,#5d4037,#8d6e63);
    color:white;
    padding:22px;
    border-radius:20px;
}
.total-box .amount{
    font-size:26px;
    font-weight:700;
}

.input-group{
    display:flex;
    flex-direction:column;
    gap:5px;
}
.input-group input,
.input-group select{
    padding:12px;
    border-radius:14px;
    border:1px solid #ddd;
}

.btn-action{
    width:100%;
    padding:14px;
    border-radius:16px;
    font-size:15px;
    font-weight:600;
    border:none;
    cursor:pointer;
    color:white;
    margin-top:10px;
}
.btn-success{ background:linear-gradient(135deg,#2e7d32,#388e3c); }
.btn-danger{ background:linear-gradient(135deg,#c62828,#e53935); }
</style>

<div class="wrapper">
<div class="card-container">
<div class="pos-layout">

<!-- LEFT -->
<div>

<div class="header">
<div>
<div class="title">คำสั่งซื้อ #{{ $order->orderid }}</div>
<div class="meta">วันที่ {{ $thaiTime }} | พนักงาน {{ $order->empname }}</div>
</div>

@if($order->payment_status === 'paid')
<span class="badge badge-paid">ชำระแล้ว</span>
@elseif($order->payment_status === 'cancelled')
<span class="badge badge-cancelled">ยกเลิกแล้ว</span>
@else
<span class="badge badge-pending">รอดำเนินการ</span>
@endif
</div>

<div class="section">
<div class="section-title">ข้อมูลลูกค้า</div>

<div class="info-grid">
<div class="info-item">
<div class="info-label">ชื่อ</div>
<div class="info-value">{{ $order->customername }}</div>
</div>
<div class="info-item">
<div class="info-label">โทร</div>
<div class="info-value">{{ $order->tel ?? '-' }}</div>
</div>
<div class="info-item">
<div class="info-label">ที่อยู่จัดส่ง</div>
<div class="info-value">{{ $order->address ?? '-' }}</div>
</div>
</div>

@if($order->tax_address_id)
<div class="tax-box">
<strong>ข้อมูลออกใบกำกับภาษี</strong><br>
ชื่อบริษัท: {{ $order->companyname ?? '-' }} <br>
เลขผู้เสียภาษี: {{ $order->taxid ?? '-' }} <br>
ที่อยู่ใบกำกับ: {{ $order->selleraddress ?? '-' }}
</div>
@endif
</div>

<div class="section">
<div class="section-title">รายการสินค้า</div>

<table>
<thead>
<tr>
<th>สินค้า</th>
<th>จำนวน</th>
<th>ราคา</th>
<th>รวม</th>
</tr>
</thead>
<tbody>
@foreach($details as $item)
<tr>
<td>{{ $item->productname }}</td>
<td>{{ $item->quantity }}</td>
<td>{{ number_format($item->price,2) }}</td>
<td>{{ number_format($item->subtotal,2) }}</td>
</tr>
@endforeach
</tbody>
</table>

<div class="summary">
ยอดรวม: {{ number_format($order->totalamount,2) }} <br>
ส่วนลด: {{ number_format($order->discount,2) }}
<div class="total">
ยอดสุทธิ: {{ number_format($order->netamount,2) }} บาท
</div>
</div>
</div>

<div class="footer">
<a href="{{ route('orders.index') }}" class="btn btn-back">กลับ</a>

@if($order->payment_status === 'paid')
<a href="{{ route('orders.receipt',$order->orderid) }}"
   class="btn btn-receipt">ดูใบเสร็จ</a>
@endif
</div>

</div>

<!-- RIGHT PAYMENT -->
<div>
<div class="payment-card">

<div class="pay-title">ชำระเงิน</div>

<div class="qr-box">
<img src="{{ asset('images/พร้อมเพย์.png') }}">
<div>สแกนเพื่อชำระเงิน</div>
</div>

<div class="total-box">
ยอดที่ต้องรับ
<div class="amount">
{{ number_format($order->netamount,2) }} บาท
</div>
</div>

@if($order->payment_status === 'pending')

<form id="payForm"
      action="{{ route('orders.pay',$order->orderid) }}"
      method="POST">
@csrf

<div class="input-group">
<label>จำนวนเงินที่ได้รับ</label>
<input type="number"
       id="received"
       name="received_amount"
       step="0.01"
       required>
</div>

<div class="input-group">
<label>เงินทอน</label>
<input type="text" id="change" readonly>
</div>

<div class="input-group">
<label>ช่องทางการชำระ</label>
<select name="payment_method" required>
<option value="">-- เลือก --</option>
<option value="cash">เงินสด</option>
<option value="promptpay">PromptPay</option>
<option value="transfer">โอนเงิน</option>
</select>
</div>

<button type="button"
        id="payBtn"
        class="btn-action btn-success">
    ชำระเงินเสร็จสิ้น
</button>

</form>

<form id="cancelForm"
      action="{{ route('orders.cancel',$order->orderid) }}"
      method="POST">
@csrf
</form>

<button type="button"
        id="cancelBtn"
        class="btn-action btn-danger">
    ยกเลิกคำสั่งซื้อ
</button>

@else
<div style="text-align:center;font-weight:600;">
ไม่สามารถดำเนินการได้ (สถานะถูกปิดแล้ว)
</div>
@endif

</div>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const total = parseFloat("{{ $order->netamount }}");
const receivedInput = document.getElementById('received');

if(receivedInput){
    receivedInput.addEventListener('input', function () {
        const received = parseFloat(this.value) || 0;
        const change = received - total;
        document.getElementById('change').value =
            change >= 0 ? change.toFixed(2) : "0.00";
    });
}

const payBtn = document.getElementById('payBtn');
if(payBtn){
    payBtn.addEventListener('click', function(){
        const received = parseFloat(receivedInput.value) || 0;
        const method = document.querySelector('[name="payment_method"]').value;

        if(received < total){
            Swal.fire('จำนวนเงินไม่พอ','กรุณากรอกเงินให้ครบ','error');
            return;
        }
        if(!method){
            Swal.fire('กรุณาเลือกช่องทางชำระเงิน','','warning');
            return;
        }

        Swal.fire({
            title: 'ยืนยันการชำระเงิน?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('payForm').submit();
            }
        });
    });
}

const cancelBtn = document.getElementById('cancelBtn');
if(cancelBtn){
    cancelBtn.addEventListener('click', function(){
        Swal.fire({
            title: 'ยกเลิกคำสั่งซื้อ?',
            text: "สต็อกสินค้าจะถูกคืน",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            confirmButtonText: 'ยืนยันยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelForm').submit();
            }
        });
    });
}
</script>

@endsection
