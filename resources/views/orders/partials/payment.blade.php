/**<div class="payment-card">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <h3 class="pay-title">💳 การชำระเงิน</h3>

    {{-- QR --}}
    <div id="qrBox" class="qr-box">
        <img src="{{ asset('images/พร้อมเพย์.png') }}">




        <div class="qr-label">สแกนเพื่อชำระผ่านพร้อมเพย์</div>
    </div>

    {{-- TOTAL --}}
    <div class="pay-total">
        <div>ยอดที่ต้องรับ</div>
        <div class="amount">
            ฿{{ number_format($order->netamount,2) }}
        </div>
    </div>

    <form method="POST" action="{{ route('orders.pay',$order->orderid) }}">
        @csrf

        <div class="input-group">
            <label>💰 จำนวนเงินที่รับ</label>
            <input type="number"
                   step="0.01"
                   name="received_amount"
                   id="received"
                   required>
        </div>

        <div class="input-group">
            <label>🔁 เงินทอน</label>
            <input type="text"
                   id="change"
                   readonly>
        </div>

        <div class="input-group">
            <label>📲 ช่องทาง</label>
            <select name="payment_method" id="method">
                <option value="cash">เงินสด</option>
                <option value="promptpay">พร้อมเพย์</option>
                <option value="transfer">โอนธนาคาร</option>
            </select>
        </div>

        <div class="btn-group">

    {{-- ชำระเงินเสร็จสิ้น --}}
    <button type="button"
        class="btn-action btn-success"
        id="payBtn"
        {{ $order->payment_status !== 'pending' ? 'disabled' : '' }}>
        ✔ ชำระเงินเสร็จสิ้น
    </button>

</div>
</form>

{{-- ยกเลิกคำสั่งซื้อ --}}
<form id="cancelForm"
      action="{{ route('orders.cancel',$order->orderid) }}"
      method="POST"
      style="display:none;">
    @csrf
</form>


</div>



            <a href="{{ route('orders.receipt',$order->orderid) }}"
               class="btn btn-receipt">
                🧾 ใบเสร็จ
            </a>
        </div>

    </form>
</div>
**/
