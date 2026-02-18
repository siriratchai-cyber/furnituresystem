@extends('layouts.app')

@section('content')

@php
    use Carbon\Carbon;
    $printTime = Carbon::now()->format('d/m/Y H:i');

    $vatRate = 0.07;
    $priceBeforeVat = $order->netamount / (1 + $vatRate);
    $vatAmount = $order->netamount - $priceBeforeVat;
@endphp

<style>
body{
    background:#f6efe9;
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}
.invoice-wrapper{
    display:flex;
    justify-content:center;
    margin:60px 0;
}
.invoice-card{
    width:800px;
    background:white;
    border-radius:24px;
    padding:50px;
    box-shadow:0 20px 60px rgba(93,64,55,.15);
}
.store-title{
    font-size:26px;
    font-weight:700;
    color:#4e342e;
    text-align:center;
}
.invoice-type{
    text-align:center;
    font-size:18px;
    font-weight:600;
    margin-top:5px;
    color:#5d4037;
}
.divider{
    height:1px;
    background:#eee;
    margin:25px 0;
}
.section-title{
    font-weight:600;
    color:#5d4037;
    margin-bottom:8px;
}
.info{
    font-size:14px;
    line-height:1.7;
}
.summary{
    text-align:right;
    font-size:15px;
}
.total{
    font-size:20px;
    font-weight:700;
    margin-top:10px;
}
.button-group{
    display:flex;
    justify-content:center;
    gap:15px;
    margin-top:35px;
}
.btn{
    padding:12px 25px;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
    color:white;
}
.btn-back{
    background:#8d6e63;
}
.btn-print{
    background:#5d4037;
}
@media print{
    .button-group{
        display:none;
    }
}
</style>

<div class="invoice-wrapper">
<div class="invoice-card">

<div class="store-title">FURNITURE STORE</div>
<div class="invoice-type">ใบกำกับภาษี (Tax Invoice)</div>

<div class="divider"></div>

<div class="section-title">ข้อมูลลูกค้า</div>
<div class="info">
    ชื่อบริษัท: {{ $order->companyname }} <br>
    ที่อยู่ออกใบกำกับ: {{ $order->taxid}} <br>
    เลขประจำตัวผู้เสียภาษี: {{ $order->selleraddress}}
</div>

<div class="divider"></div>
<div class="section-title">รายละเอียดการสั่งซื้อ</div>
<div class="info">
    เลขที่คำสั่งซื้อ: {{ $order->orderid }} <br>
    วันที่สั่งซื้อ: {{ Carbon::parse($order->orderdate)->format('d/m/Y H:i') }} <br>
    วันที่พิมพ์: {{ $printTime }}
</div>

<div class="divider"></div>

<div class="summary">
    <div>มูลค่าสินค้าก่อนภาษี: ฿{{ number_format($priceBeforeVat,2) }}</div>
    <div>ภาษีมูลค่าเพิ่ม 7%: ฿{{ number_format($vatAmount,2) }}</div>
    <div class="total">
        รวมทั้งสิ้น (รวม VAT): ฿{{ number_format($order->netamount,2) }}
    </div>
</div>

<div class="divider"></div>

<div style="text-align:center; font-size:13px;">
    เอกสารฉบับนี้เป็นใบกำกับภาษีอย่างย่อ
</div>

<div class="button-group">
    <a href="{{ route('orders.show',$order->orderid) }}"
       class="btn btn-back">
        ← กลับ
    </a>

    <button onclick="window.print()"
            class="btn btn-print">
        🖨 พิมพ์ใบกำกับภาษี
    </button>
</div>

</div>
</div>

@endsection
