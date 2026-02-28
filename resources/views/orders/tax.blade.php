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


<link rel="stylesheet" href="{{ asset('css/order-tax.css') }}">


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
            <th width="13%">ลำดับ</th>
            <th width="27%">รายการ</th>
            <th width="15%">จำนวน</th>
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
