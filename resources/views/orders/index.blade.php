@extends('layouts.app')

@section('content')

<style>
.order-box {
    width: 100%;
    max-width: 1000px;
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

th {
    background: #f4f6f9;
}

@media(max-width:768px){
    table {
        font-size: 14px;
    }
}
</style>

<div class="order-box">

    <h2>รายการคำสั่งซื้อ</h2>

    <table>
        <thead>
            <tr>
                <th>รหัสคำสั่งซื้อ</th>
                <th>วันที่</th>
                <th>ลูกค้า</th>
                <th>พนักงาน</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->orderid }}</td>
                    <td>{{ $order->orderdate }}</td>
                    <td>{{ $order->customername }}</td>
                    <td>{{ $order->empname }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">ไม่มีข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
