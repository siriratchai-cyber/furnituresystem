<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Orders
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $orders = DB::table('Order')
            ->join('customer', 'Order.customerid', '=', 'customer.customerid')
            ->join('employee', 'Order.employeeid', '=', 'employee.employeeid')
            ->select(
                'Order.*',
                'customer.customername',
                'employee.empname'
            )
            ->orderBy('Order.orderid', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Order Page
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $customers = DB::table('customer')->get();
        $products  = DB::table('product')->get();

        $lowStockProducts = DB::table('product')
            ->where('stock', '<=', 5)
            ->get();

        return view('orders.create', compact(
            'customers',
            'products',
            'lowStockProducts'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Store Order
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            // =========================
            // Validate
            // =========================
            $request->validate([
                'customername' => 'required',
                'total'        => 'required|numeric',
                'net'          => 'required|numeric',
                'products'     => 'required'
            ]);

            if (!Session::has('employee_id')) {
                DB::rollBack();
                return redirect()->route('login');
            }

            // =========================
            // Find Customer
            // =========================
           $customer = DB::table('customer')
    ->where('customername', $request->customername)
    ->first();

if (!$customer) {

    // Generate Customer ID เช่น CU0001
    $lastCustomer = DB::table('customer')
        ->orderBy('customerid', 'desc')
        ->lockForUpdate()
        ->first();

    $newNumber = $lastCustomer
        ? ((int) substr($lastCustomer->customerid, 2)) + 1
        : 1;

    $customerId = 'CU' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    DB::table('customer')->insert([
        'customerid'   => $customerId,
        'customername' => $request->customername,
        'tel'          => $request->tel,
        'address'      => $request->address
    ]);

    $customer = (object)[
        'customerid' => $customerId
    ];
}


            $products = json_decode($request->products, true);

            if (!is_array($products) || count($products) === 0) {
                DB::rollBack();
                return back()->with('error', 'กรุณาเพิ่มสินค้า');
            }

            // =========================
            // Generate Order ID (OD0001)
            // =========================
            $lastOrder = DB::table('Order')
                ->orderBy('orderid', 'desc')
                ->lockForUpdate()
                ->first();

            $newNumber = $lastOrder
                ? ((int) substr($lastOrder->orderid, 2)) + 1
                : 1;

            $orderId = 'OD' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $discount = $request->total - $request->net;

            // =========================
            // Insert Order
            // =========================
            DB::table('Order')->insert([
                'orderid'    => $orderId,
                'orderdate'  => now(),
                'employeeid' => session('employee_id'),
                'customerid' => $customer->customerid,
                'totalamount'=> $request->total,
                'discount'   => $discount,
                'netamount'  => $request->net
            ]);

            // =========================
            // Insert Sales Detail
            // =========================
            foreach ($products as $item) {

                $product = DB::table('product')
                    ->where('productid', $item['productid'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    DB::rollBack();
                    return back()->with('error', 'ไม่พบสินค้า ' . $item['productid']);
                }

                if ($product->stock < (int)$item['quantity']) {
                    DB::rollBack();
                    return back()->with(
                        'error',
                        'สินค้า ' . $item['productid'] .
                        ' มีไม่พอ (เหลือ ' . $product->stock . ')'
                    );
                }

                // Generate DT0001
                $lastDetail = DB::table('sales_detail')
                    ->orderBy('orderdetailid', 'desc')
                    ->lockForUpdate()
                    ->first();

                $newDetailNumber = $lastDetail
                    ? ((int) substr($lastDetail->orderdetailid, 2)) + 1
                    : 1;

                $orderDetailId = 'DT' . str_pad($newDetailNumber, 4, '0', STR_PAD_LEFT);

                DB::table('sales_detail')->insert([
                    'orderdetailid' => $orderDetailId,
                    'orderid'       => $orderId,
                    'productid'     => $item['productid'],
                    'quantity'      => (int)$item['quantity'],
                    'price'         => $item['price'],
                    'subtotal'      => $item['subtotal']
                ]);

                DB::table('product')
                    ->where('productid', $item['productid'])
                    ->decrement('stock', (int)$item['quantity']);
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'บันทึกสำเร็จ');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
