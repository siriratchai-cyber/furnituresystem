@extends('layouts.app')

@section('content')

@php
    use Carbon\Carbon;

    $printTime = Carbon::now('Asia/Bangkok')->format('d/m/Y H:i');
    $vatRate = 0.07;

    $totalWithVat = $order->totalamount ?? 0;   // ราคารวมก่อนหักส่วนลด
    $discount     = $order->discount ?? 0;
    $netTotal     = $order->netamount ?? 0;     // ยอดสุทธิ (รวม VAT แล้ว)

    // คำนวณย้อน VAT จากยอดสุทธิ
    $priceBeforeVat = $netTotal / (1 + $vatRate);
    $vatAmount      = $netTotal - $priceBeforeVat;
@endphp

<link rel="stylesheet" href="{{ asset('css/order-reciept.css') }}">

<div class="receipt-wrapper">
<div class="receipt-card">

<div class="store-name">FURNITURE STORE</div>
<div class="receipt-meta">
    ใบเสร็จเลขที่: {{ $receipt->receiptid }} <br>
    วันที่สั่งซื้อ: {{ \Carbon\Carbon::parse($order->orderdate)->format('d/m/Y H:i') }}
</div>

<div class="paid-badge">✓ ชำระเงินแล้ว</div>

<div class="divider"></div>

<strong>ลูกค้า:</strong> {{ $order->customername }} <br>
<strong>เบอร์โทร:</strong> {{ $order->tel ?? '-' }} <br>
<strong>ผู้ดำเนินการ:</strong> {{ $order->empname }}

<div class="divider"></div>

<table>
    <thead>
        <tr>
            <th>สินค้า</th>
            <th align="center">จำนวน</th>
            <th align="right">ราคา</th>
            <th align="right">รวม</th>
        </tr>
    </thead>
    <tbody>
    @foreach($details as $item)
        <tr>
            <td>{{ $item->productname }}</td>
            <td align="center">{{ $item->quantity }}</td>
            <td align="right">{{ number_format($item->price,2) }}</td>
            <td align="right">{{ number_format($item->subtotal,2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="divider"></div>

<div class="summary-row">
    <span>ราคาสินค้ารวม (รวม VAT)</span>
    <span>{{ number_format($totalWithVat,2) }}</span>
</div>

<div class="summary-row">
    <span>ส่วนลด</span>
    <span>- {{ number_format($discount,2) }}</span>
</div>

<div class="summary-row">
    <span>ยอดสุทธิหลังหักส่วนลด</span>
    <span>{{ number_format($netTotal,2) }}</span>
</div>

<div class="divider"></div>

<div class="summary-row">
    <span>ราคาก่อน VAT</span>
    <span>{{ number_format($priceBeforeVat,2) }}</span>
</div>

<div class="summary-row">
    <span>ภาษีมูลค่าเพิ่ม 7%</span>
    <span>{{ number_format($vatAmount,2) }}</span>
</div>

<div class="divider"></div>

<div class="summary-row total">
    <span>จำนวนเงินทั้งสิ้น</span>
    <span>{{ number_format($netTotal,2) }} บาท</span>
</div>


<div class="divider"></div>

<div class="footer-text">
    วันที่พิมพ์: {{ $printTime }} <br>
    ขอบคุณที่ใช้บริการ 🙏
</div>

<div class="button-group">
    <a href="{{ route('orders.show',$order->orderid) }}"
       class="btn btn-back">
        ← กลับ
    </a>

    <button onclick="window.print()"
            class="btn btn-print">
        🖨 พิมพ์ใบเสร็จ
    </button>

    @if(!empty($order->tax_address_id))
    <a href="{{ route('orders.tax',$order->orderid) }}"
       class="btn btn-tax">
        ออกใบกำกับภาษี
    </a>
@else
    <button class="btn btn-tax btn-disabled" disabled>
        ออกใบกำกับภาษี
    </button>
@endif

</div>

</div>
</div>

@endsection
