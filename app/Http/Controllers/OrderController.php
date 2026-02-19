<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Receipt;
use App\Models\Order;

class OrderController extends Controller
{

    /* =====================================================
       INDEX
    ===================================================== */
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->join('customer','orders.customerid','=','customer.customerid')
            ->select(
                'orders.orderid',
                'orders.orderdate',
                'orders.netamount',
                'orders.payment_status',
                'customer.customername'
            );

        if ($request->search) {
            $query->where('orders.orderid','like','%'.$request->search.'%');
        }

       if ($request->filled('status') && $request->status != 'all') {
    $query->where('orders.payment_status', $request->status);
}


        $orders = $query->orderByDesc('orders.orderdate')->paginate(10);

        return view('orders.index',compact('orders'));
    }


    /* =====================================================
       CREATE
    ===================================================== */
    public function create()
    {
        $products  = DB::table('product')->get();
        $customers = DB::table('customer')->get();

        return view('orders.create', compact('products','customers'));
    }


    /* =====================================================
       STORE
    ===================================================== */
    public function store(Request $request)
{
    $request->validate([
        'customer_name'    => 'required|string|max:255',
        'customer_address' => 'required|string',
        'products'         => 'required|string',
    ]);

    DB::beginTransaction();

    try {

$lastCustomer = DB::table('customer')
    ->lockForUpdate()
    ->orderByDesc('customerid')
    ->first();

                $newCustomerId = $lastCustomer
            ? 'CU'.str_pad(intval(substr($lastCustomer->customerid,2))+1,4,'0',STR_PAD_LEFT)
            : 'CU0001';

        DB::table('customer')->insert([
            'customerid'   => $newCustomerId,
            'customername' => $request->customer_name,
            'tel'          => $request->tel,
            'address'      => $request->customer_address,
        ]);

        $taxAddressId = null;

        if ($request->tax_number) {

            $lastTax = DB::table('tax_address')->orderByDesc('taxaddressid')->first();

            $taxAddressId = $lastTax
                ? 'TA'.str_pad(intval(substr($lastTax->taxaddressid,2))+1,4,'0',STR_PAD_LEFT)
                : 'TA0001';

            DB::table('tax_address')->insert([
                'taxaddressid' => $taxAddressId,
                'customerid'   => $newCustomerId,
                'companyname'  => $request->tax_company,
                'taxid'        => $request->tax_number,
                'selleraddress'=> $request->customer_address,
            ]);
        }

       $lastOrder = DB::table('orders')
    ->lockForUpdate()
    ->orderByDesc('orderid')
    ->first();
   $newOrderId = $lastOrder
            ? 'OR'.str_pad(intval(substr($lastOrder->orderid,2))+1,4,'0',STR_PAD_LEFT)
            : 'OR0001';

        $products = json_decode($request->products, true);

        $total = 0;

        DB::table('orders')->insert([
            'orderid'        => $newOrderId,
            'customerid'     => $newCustomerId,
            'employeeid'     => session('employeeid'),
            'orderdate'      => now(),
            'totalamount'    => 0,
            'discount'       => $request->discount ?? 0,
            'netamount'      => 0,
            'payment_status' => 'pending',
            'tax_address_id' => $taxAddressId,
        ]);

        foreach ($products as $item) {

            $product = DB::table('product')
                ->where('productid',$item['productid'])
                ->lockForUpdate()
                ->first();

            if(!$product){
                throw new \Exception('ไม่พบสินค้า');
            }

            if($product->stock < $item['quantity']){
                throw new \Exception('สต็อกไม่พอ');
            }

            $price = $product->price;
            $subtotal = $price * $item['quantity'];

            $total += $subtotal;

            DB::table('sales_detail')->insert([
                'orderdetailid' => uniqid('DT'),
                'orderid'       => $newOrderId,
                'productid'     => $item['productid'],
                'quantity'      => $item['quantity'],
                'price'         => $price,
                'subtotal'      => $subtotal
            ]);

            DB::table('product')
                ->where('productid',$item['productid'])
                ->decrement('stock',$item['quantity']);
        }

        $discount = $request->discount ?? 0;
        $net = max($total - $discount, 0);

        DB::table('orders')
            ->where('orderid',$newOrderId)
            ->update([
                'totalamount' => $total,
                'netamount'   => $net
            ]);
DB::commit();

return redirect()->route('orders.show', $newOrderId)
    ->with('success','เพิ่มคำสั่งซื้อสำเร็จ');


    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error',$e->getMessage());
    }
}


    /* =====================================================
       SHOW
    ===================================================== */
    public function show($id)
    {
      $order = DB::table('orders')
    ->leftJoin('customer','orders.customerid','=','customer.customerid')
    ->leftJoin('employee','orders.employeeid','=','employee.employeeid')
    ->leftJoin('tax_address','orders.tax_address_id','=','tax_address.taxaddressid')
    ->select(
        'orders.*',
        'customer.customername',
        'customer.tel',
        'customer.address',
        'tax_address.companyname',
        'tax_address.taxid',
        'tax_address.selleraddress',
        'tax_address.addresstype',
        'employee.empname'
    )
    ->where('orders.orderid',$id)
    ->first();



        if(!$order){
            return redirect()->route('orders.index')
                ->with('error','ไม่พบคำสั่งซื้อ');
        }

        $details = DB::table('sales_detail')
            ->join('product','sales_detail.productid','=','product.productid')
            ->where('sales_detail.orderid',$id)
            ->get();

        return view('orders.show',compact('order','details'));
    }


    /* =====================================================
       RECEIPT
    ===================================================== */
    public function receipt($id)
{
    $order = DB::table('orders')
        ->leftJoin('customer','orders.customerid','=','customer.customerid')
        ->leftJoin('employee','orders.employeeid','=','employee.employeeid')
        ->leftJoin('tax_address','orders.tax_address_id','=','tax_address.taxaddressid')
        ->select(
            'orders.*',
            'customer.customername',
            'customer.tel',
            'employee.empname',
            'tax_address.companyname',
            'tax_address.taxid',
            'tax_address.selleraddress'
        )
        ->where('orders.orderid',$id)
        ->first();

    if(!$order){
        return redirect()->route('orders.index');
    }

    $receipt = DB::table('receipt')
        ->where('orderid',$id)
        ->first();

    if(!$receipt){
        return redirect()->route('orders.show',$id)
            ->with('error','ยังไม่มีใบเสร็จ');
    }

    $details = DB::table('sales_detail')
        ->join('product','sales_detail.productid','=','product.productid')
        ->select(
            'product.productname',
            'sales_detail.quantity',
            'sales_detail.price',
            DB::raw('(sales_detail.quantity * sales_detail.price) as subtotal')
        )
        ->where('sales_detail.orderid',$id)
        ->get();

    return view('orders.receipts',
        compact('order','receipt','details')
    );
}


    /* =====================================================
       TAX INVOICE
    ===================================================== */
public function tax($id)
{
    $order = DB::table('orders')
        ->leftJoin('customer','orders.customerid','=','customer.customerid')
        ->leftJoin('tax_address','orders.tax_address_id','=','tax_address.taxaddressid')
        ->select(
            'orders.*',
            'customer.customername',
            'customer.tel',
            'tax_address.companyname',
            'tax_address.taxid',
            'tax_address.selleraddress'
        )
        ->where('orders.orderid',$id)
        ->first();

    if(!$order){
        return redirect()->route('orders.index')
            ->with('error','ไม่พบคำสั่งซื้อ');
    }

    $details = DB::table('sales_detail')
        ->join('product','sales_detail.productid','=','product.productid')
        ->select(
            'product.productname',
            'sales_detail.quantity',
            'sales_detail.price',
            DB::raw('(sales_detail.quantity * sales_detail.price) as subtotal')
        )
        ->where('sales_detail.orderid',$id)
        ->get();

    return view('orders.tax', compact('order','details'));
}





    /* =====================================================
       PAY
    ===================================================== */
    public function pay(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $order = Order::where('orderid',$id)
            ->lockForUpdate()
            ->firstOrFail();

        if($order->payment_status !== 'pending'){
            throw new \Exception('คำสั่งซื้อนี้ชำระแล้วหรือถูกยกเลิก');
        }

        if($request->received_amount < $order->netamount){
            throw new \Exception('จำนวนเงินที่รับมาไม่เพียงพอ');
        }

        $order->payment_status = 'paid';
        $order->save();

        $last = Receipt::orderBy('receiptid','desc')->lockForUpdate()->first();
        $number = $last ? intval(substr($last->receiptid,2)) + 1 : 1;
        $receiptId = 'RC'.str_pad($number,4,'0',STR_PAD_LEFT);

        Receipt::create([
            'receiptid'         => $receiptId,
            'orderid'           => $order->orderid,
            'paymentmethod'     => $request->payment_method,
            'totalmoneyamount'  => $order->netamount,
            'receivedmoneyamount'=> $request->received_amount,
            'changemoneyamount' => $request->received_amount - $order->netamount,
        ]);

        DB::commit();

        return redirect()->route('orders.receipt',$id);

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error',$e->getMessage());
    }
}


    public function filter(Request $request)
{
    $query = DB::table('orders')
        ->join('customer','orders.customerid','=','customer.customerid')
        ->select(
            'orders.orderid',
            'orders.orderdate',
            'orders.netamount',
            'orders.payment_status',
            'customer.customername'
        );

    if ($request->q) {
        $query->where(function($q) use ($request) {
            $q->where('orders.orderid','like','%'.$request->q.'%')
              ->orWhere('customer.customername','like','%'.$request->q.'%');
        });
    }

    if ($request->filled('status') && $request->status !== 'all') {
    $query->where('orders.payment_status', $request->status);
}

    $orders = $query->orderByDesc('orders.orderdate')->get();

    return response()->json($orders);
}

public function cancel($id)
{
    DB::beginTransaction();

    try {

        $order = Order::where('orderid',$id)
            ->lockForUpdate()
            ->firstOrFail();

        if($order->payment_status === 'cancelled'){
            return redirect()->route('orders.index')
                ->with('error','คำสั่งซื้อนี้ถูกยกเลิกแล้ว');
        }

        if($order->payment_status === 'paid'){
            throw new \Exception('ไม่สามารถยกเลิกคำสั่งซื้อที่ชำระเงินแล้ว');
        }

        $details = DB::table('sales_detail')
            ->where('orderid',$id)
            ->get();

        foreach($details as $item){
            DB::table('product')
                ->where('productid',$item->productid)
                ->increment('stock',$item->quantity);
        }

        $order->payment_status = 'cancelled';
        $order->save();

        DB::commit();

        return redirect()->route('orders.index')
            ->with('success','ยกเลิกคำสั่งซื้อและคืนสินค้าแล้ว');

    } catch(\Exception $e){

        DB::rollBack();
        return back()->with('error',$e->getMessage());
    }
}



}
