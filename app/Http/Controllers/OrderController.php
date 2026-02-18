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

        if ($request->status) {
            $query->where('orders.payment_status',$request->status);
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

            /* -------------------------
               1️⃣ Create Customer
            ------------------------- */
            $lastCustomer = DB::table('customer')->orderByDesc('customerid')->first();
            $newCustomerId = $lastCustomer
                ? 'CU'.str_pad(intval(substr($lastCustomer->customerid,2))+1,4,'0',STR_PAD_LEFT)
                : 'CU0001';

            DB::table('customer')->insert([
                'customerid'   => $newCustomerId,
                'customername' => $request->customer_name,
                'tel'          => $request->tel,
                'address'      => $request->customer_address,
            ]);

            /* -------------------------
               2️⃣ Create Tax Address (optional)
            ------------------------- */
            $taxAddressId = null;

            if ($request->tax_number) {

                $lastTax = DB::table('tax_address')
                    ->orderByDesc('taxaddressid')
                    ->first();

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

            /* -------------------------
               3️⃣ Create Order
            ------------------------- */
            $lastOrder = DB::table('orders')->orderByDesc('orderid')->first();
            $newOrderId = $lastOrder
                ? 'OR'.str_pad(intval(substr($lastOrder->orderid,2))+1,4,'0',STR_PAD_LEFT)
                : 'OR0001';

            DB::table('orders')->insert([
                'orderid'        => $newOrderId,
                'customerid'     => $newCustomerId,
                'employeeid'     => session('employeeid'),
                'orderdate'      => now(),
                'totalamount'    => $request->total ?? 0,
                'discount'       => $request->discount ?? 0,
                'netamount'      => $request->netamount ?? 0,
                'payment_status' => 'pending',
                'tax_address_id' => $taxAddressId,
            ]);

            /* -------------------------
               4️⃣ Insert Order Detail
            ------------------------- */
            $products = json_decode($request->products, true);

            foreach ($products as $item) {

                DB::table('sales_detail')->insert([
                    'orderdetailid' => uniqid('DT'),
                    'orderid'       => $newOrderId,
                    'productid'     => $item['productid'],
                    'quantity'      => $item['quantity'],
                    'price'         => $item['price'],
                    'subtotal'      => $item['price'] * $item['quantity']
                ]);

                DB::table('product')
                    ->where('productid',$item['productid'])
                    ->decrement('stock',$item['quantity']);
            }

            DB::commit();

            return redirect()->route('orders.index')
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
    $order = Order::with('taxAddress')
        ->where('orderid',$id)
        ->first();

    if(!$order || is_null($order->tax_address_id)){
        return redirect()
            ->route('orders.receipt',$id)
            ->with('error','ไม่มีข้อมูลใบกำกับภาษี');
    }

    return view('orders.tax',compact('order'));
}



    /* =====================================================
       PAY
    ===================================================== */
    public function pay(Request $request, $id)
    {
        $order = Order::where('orderid',$id)->firstOrFail();

        $order->payment_status = 'paid';
        $order->save();

        $last = Receipt::orderBy('receiptid','desc')->first();
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

        return redirect()->route('orders.receipt',$id);
    }
}
