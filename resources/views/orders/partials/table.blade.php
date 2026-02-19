<div class="table-responsive">

<table class="table align-middle"
       style="
           background:white;
           border-radius:18px;
           overflow:hidden;
       ">

    <thead style="
        background:linear-gradient(90deg,#8b6a4f,#a67c52);
        color:white;
    ">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th class="text-end"></th>
        </tr>
    </thead>

    <tbody>

    @forelse($orders as $order)
        <tr style="border-bottom:1px solid #eee;">

            <td class="fw-bold text-dark">
                #{{ $order->orderid }}
            </td>

            <td>{{ $order->customername ?? '-' }}</td>

            <td class="fw-semibold text-dark">
                {{ number_format($order->netamount,2) }} ฿
            </td>

            <td>
                @if($order->payment_status === 'paid')
    <span class="badge bg-success">Paid</span>

@elseif($order->payment_status === 'cancelled')
    <span class="badge bg-danger">Cancelled</span>

@else
    <span class="badge bg-warning text-dark">Pending</span>
@endif

            </td>

            <td>
                {{ \Carbon\Carbon::parse($order->orderdate)->format('d M Y H:i') }}
            </td>

            <td class="text-end">

                <a href="{{ route('orders.show',$order->orderid) }}"
                   class="btn btn-sm shadow-sm"
                   style="
                       background:#6b4f3a;
                       color:white;
                       border-radius:10px;
                       font-weight:500;
                   ">

                    ดูรายละเอียดคำสั่งซื้อ

                </a>

            </td>

        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center py-4 text-muted">
                No orders found
            </td>
        </tr>
    @endforelse

    </tbody>
</table>

</div>

<div class="d-flex justify-content-end mt-3">
    {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
</div>
