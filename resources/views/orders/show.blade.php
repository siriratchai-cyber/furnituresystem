@extends('layouts.app')

@section('content')

@php
use Carbon\Carbon;
$thaiTime = Carbon::parse($order->orderdate)
        ->timezone('Asia/Bangkok')
        ->format('d/m/Y H:i');
@endphp

<link rel="stylesheet" href="{{ asset('css/order-show.css') }}">

<div class="wrapper">
<div class="card-container">
<div class="pos-layout">

<!-- LEFT -->
<div>

<div class="header">
<div>
<div class="title">คำสั่งซื้อ #{{ $order->orderid }}</div>
<div class="meta">วันที่ {{ $thaiTime }} | ผู้ดำเนินการ {{ $order->empname }}</div>
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

{{-- ============================= --}}
{{-- ===== สถานะรอดำเนินการ ===== --}}
{{-- ============================= --}}
@if($order->payment_status === 'pending')

    {{-- ===== ฟอร์มชำระเงิน ===== --}}
    <form id="payForm"
          action="{{ route('orders.pay',$order->orderid) }}"
          method="POST">
        @csrf

        <div class="input-group">
            <label>ช่องทางชำระเงิน</label>
            <select name="payment_method" required>
                <option value="">-- เลือกช่องทาง --</option>
                <option value="cash">เงินสด</option>
                <option value="transfer">โอนเงิน</option>
                <option value="card">บัตรเครดิต</option>
            </select>
        </div>

        <div class="input-group">
            <label>รับเงินมา</label>
            <input type="number"
                   id="received"
                   name="received_amount"
                   step="0.01"
                   placeholder="0.00"
                   required>
        </div>

        <div class="input-group">
            <label>เงินทอน</label>
            <input type="text"
                   id="change"
                   readonly
                   value="0.00">
        </div>

        <button type="button"
                id="payBtn"
                class="btn-action btn-success">
            ยืนยันการชำระเงิน
        </button>

    </form>

    {{-- ===== ฟอร์มยกเลิก ===== --}}
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


{{-- ============================= --}}
{{-- ===== สถานะชำระแล้ว ===== --}}
{{-- ============================= --}}
@elseif($order->payment_status === 'paid')

    <div style="
        text-align:center;
        padding:15px;
        font-weight:600;
        color:#2e7d32;">
        ชำระเงินเรียบร้อยแล้ว
    </div>


{{-- ============================= --}}
{{-- ===== สถานะยกเลิก ===== --}}
{{-- ============================= --}}
@elseif($order->payment_status === 'cancelled')

    <div style="
        text-align:center;
        padding:15px;
        font-weight:600;
        color:#c62828;">
        คำสั่งซื้อถูกยกเลิกแล้ว
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
            Swal.fire('จำนวนเงินไม่พอ','กรุณากรอกข้อมูลให้ครบ','error');
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
