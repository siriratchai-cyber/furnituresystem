@extends('layouts.app')

@section('content')

@php
use Carbon\Carbon;

$printTime = Carbon::now()->format('d/m/Y H:i');
$vatRate = 0.07;

/* ------------------------
   ค่าจากฐานข้อมูล
------------------------ */
$totalWithVat = $order->totalamount ?? 0;   // ราคารวมก่อนหักส่วนลด
$discount     = $order->discount ?? 0;      // ส่วนลด
$netTotal     = $order->netamount ?? 0;     // ยอดสุทธิหลังหักส่วนลด (รวม VAT แล้ว)

/* ------------------------
   คำนวณ VAT จากยอดสุทธิจริง
------------------------ */
$priceBeforeVat = $netTotal / (1 + $vatRate);
$vatAmount      = $netTotal - $priceBeforeVat;
@endphp


<style>
body{
    background:#f6efe9;
    font-family:"Sarabun","Segoe UI",sans-serif;
}

.invoice-wrapper{
    display:flex;
    justify-content:center;
    margin:50px 0;
}

.invoice-outer{
    width:900px;
    background:white;
    padding:30px;
    border:3px solid #000;
}

.invoice-inner{
    border:2px solid #000;
    padding:35px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
}

.store-info{
    font-size:14px;
    line-height:1.7;
}

.invoice-title{
    text-align:center;
    font-size:22px;
    font-weight:700;
}

.meta{
    font-size:14px;
    text-align:right;
}

.section{
    margin-top:20px;
    font-size:14px;
    line-height:1.7;
}

.table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

.table th,
.table td{
    border:1px solid #000;
    padding:8px;
    font-size:14px;
}

.table th{
    background:#f0f0f0;
}

.summary{
    margin-top:15px;
    width:300px;
    float:right;
    font-size:14px;
}

.summary div{
    display:flex;
    justify-content:space-between;
    padding:4px 0;
}

.summary .total{
    font-weight:700;
    font-size:16px;
}

.clearfix::after{
    content:"";
    display:block;
    clear:both;
}

.sign-section{
    margin-top:60px;
    display:flex;
    justify-content:space-between;
    text-align:center;
    font-size:14px;
}

.sign-box{
    width:40%;
}

.sign-line{
    margin-top:50px;
    border-top:1px solid #000;
    padding-top:5px;
}

.button-group{
    margin-top:40px;
    text-align:center;
}

.btn{
    padding:10px 25px;
    border-radius:5px;
    background:#5d4037;
    color:white;
    text-decoration:none;
    font-weight:600;
    margin:0 5px;
}

@media print{
    body{ background:white; }
    .button-group{ display:none; }
}
</style>

<div class="invoice-wrapper">
<div class="invoice-outer">
<div class="invoice-inner">

{{-- Header --}}
<div class="header">
    <div class="store-info">
        <strong>FURNITURE STORE</strong><br>
        123 ถนนสุขุมวิท แขวงคลองตัน กรุงเทพฯ 10110 <br>
        เลขประจำตัวผู้เสียภาษี 0100000000000 <br>
        โทร 02-000-0000
    </div>

    <div class="meta">
        <div><strong>Invoice ID:</strong> {{ $order->orderid }}</div>
        <div><strong>วันที่:</strong> {{ Carbon::parse($order->orderdate)->format('d/m/Y') }}</div>
    </div>
</div>

<div class="invoice-title">
    ใบกำกับภาษี (Tax Invoice)
</div>

<hr>

{{-- ผู้ซื้อ --}}
<div class="section">
    <strong>ผู้ซื้อ</strong><br>
    ชื่อบริษัท/ลูกค้า: {{ $order->companyname ?? $order->customername }} <br>
    ที่อยู่: {{ $order->selleraddress ?? $order->address }} <br>
    เลขประจำตัวผู้เสียภาษี: {{ $order->taxid ?? '-' }} <br>
    โทร: {{ $order->tel ?? '-' }}
</div>

{{-- ตารางสินค้า --}}
<table class="table">
    <thead>
        <tr>
            <th width="5%">ลำดับ</th>
            <th width="45%">รายการ</th>
            <th width="10%">จำนวน</th>
            <th width="20%">ราคาต่อหน่วย</th>
            <th width="20%">จำนวนเงิน</th>
        </tr>
    </thead>
    <tbody>
        @foreach($details as $index => $item)
        <tr>
            <td align="center">{{ $index+1 }}</td>
            <td>{{ $item->productname }}</td>
            <td align="center">{{ $item->quantity }}</td>
            <td align="right">{{ number_format($item->price,2) }}</td>
            <td align="right">{{ number_format($item->subtotal,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="summary">

    <div>
        <span>ราคาสินค้ารวม (รวม VAT)</span>
        <span>{{ number_format($totalWithVat,2) }}</span>
    </div>

    <div>
        <span>ส่วนลด</span>
        <span>- {{ number_format($discount,2) }}</span>
    </div>

    <div>
        <span>ยอดสุทธิหลังหักส่วนลด</span>
        <span>{{ number_format($netTotal,2) }}</span>
    </div>

    <hr>

    <div>
        <span>ภาษีมูลค่าเพิ่ม 7%</span>
        <span>{{ number_format($vatAmount,2) }}</span>
    </div>

    <div>
        <span>ราคาก่อน VAT</span>
        <span>{{ number_format($priceBeforeVat,2) }}</span>
    </div>

    <div class="total">
        <span>จำนวนเงินทั้งสิ้น</span>
        <span>{{ number_format($netTotal,2) }}</span>
    </div>

</div>


<div class="clearfix"></div>

{{-- ลายเซ็น --}}
<div class="sign-section">
    <div class="sign-box">
        ลงชื่อ........................................ ผู้รับสินค้า
        <div class="sign-line">
            ( {{ $order->customername }} )
        </div>
    </div>

    <div class="sign-box">
        ลงชื่อ........................................ ผู้ขาย
        <div class="sign-line">
            ( FURNITURE STORE )
        </div>
    </div>
</div>

<div class="button-group">
    <a href="{{ route('orders.show',$order->orderid) }}" class="btn">← กลับ</a>
    <a href="#" onclick="window.print()" class="btn">🖨 พิมพ์</a>
</div>

</div>
</div>
</div>

@endsection
