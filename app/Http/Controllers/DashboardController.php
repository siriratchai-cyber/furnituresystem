<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        // ===== KPI =====

        $todaySales = DB::table('orders')
            ->whereDate('orderdate', today())
            ->where('payment_status', 'paid')
            ->sum('netamount');

        $todayOrders = DB::table('orders')
            ->whereDate('orderdate', today())
            ->count();

        $pendingOrders = DB::table('orders')
            ->where('payment_status', 'pending')
            ->count();

        // 🔥 ตรงนี้แก้จาก products → product
        $lowStock = DB::table('product')
            ->where('stock', '<=', 5)
            ->count();

        // ===== Latest Orders =====

        // 🔥 customers → customer
        $latestOrders = DB::table('orders')
            ->join('customer', 'orders.customerid', '=', 'customer.customerid')
            ->select(
                'orders.orderid',
                'orders.orderdate',
                'orders.netamount',
                'orders.payment_status',
                'customer.customername'
            )
            ->orderByDesc('orders.orderdate')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayOrders',
            'pendingOrders',
            'lowStock',
            'latestOrders'
        ));
    }

    public function data()
{
    // ===== SUMMARY =====
    $summary = [
        'pending' => DB::table('orders')
            ->where('payment_status', 'pending')
            ->count(),

        'paid' => DB::table('orders')
            ->where('payment_status', 'paid')
            ->count(),
    ];

    // ====================================
// WEEKLY SALES (CALENDAR WEEK Mon–Sun)
// ====================================

$start = now()->startOfWeek();
$end = now()->endOfWeek()->endOfDay();
  

$weeklySales = DB::table('orders')
    ->selectRaw("DATE(orderdate) as sale_date, SUM(netamount) as total")
    ->where('payment_status', 'paid')
    ->whereBetween('orderdate', [$start, $end])
    ->groupByRaw("DATE(orderdate)")
    ->orderBy('sale_date')
    ->get()
    ->keyBy('sale_date');

$labels = [];
$data   = [];

for ($i = 0; $i < 7; $i++) {

    $dateObj = now()->startOfWeek()->addDays($i);

    $dateKey = $dateObj->format('Y-m-d');

    $labels[] = $dateObj->format('D'); 
    // ถ้าอยากได้ 01/02 ใช้ ->format('d/m')

    $data[] = $weeklySales[$dateKey]->total ?? 0;
}
return response()->json([
    'summary' => $summary,
    'todaySales' => DB::table('orders')
        ->whereDate('orderdate', today())
        ->where('payment_status', 'paid')
        ->sum('netamount'),

    'lowStock' => DB::table('product')
        ->where('stock', '<=', 5)
        ->get(),

    'critical' => DB::table('product')
        ->where('stock', '<=', 3)
        ->get(),

    'chart' => [
        'labels' => $labels,
        'data'   => $data
    ]
]);


}

public function salesSummaryData(Request $request)
{
    $period = $request->period ?? 'week';

    if ($period === 'week') {
        $start = now()->startOfWeek();
        $end   = now()->endOfWeek()->endOfDay();

        $groupSales   = "DATE(orderdate)";
        $groupExpense = "DATE(orders.orderdate)";
        $keyFormat    = "Y-m-d";

        $prevStart = now()->subWeek()->startOfWeek();
        $prevEnd   = now()->subWeek()->endOfWeek()->endOfDay();

    } elseif ($period === 'month') {

        $start = now()->startOfMonth();
        $end   = now()->endOfMonth()->endOfDay();

        $groupSales   = "DATE(orderdate)";
        $groupExpense = "DATE(orders.orderdate)";
        $keyFormat    = "Y-m-d";

        $prevStart = now()->subMonth()->startOfMonth();
        $prevEnd   = now()->subMonth()->endOfMonth()->endOfDay();

    } else {

        $start = now()->startOfYear();
        $end   = now()->endOfYear()->endOfDay();

        $groupSales   = "TO_CHAR(orderdate,'YYYY-MM')";
$groupExpense = "TO_CHAR(orders.orderdate,'YYYY-MM')";

        $keyFormat    = "Y-m";

        $prevStart = now()->subYear()->startOfYear();
        $prevEnd   = now()->subYear()->endOfYear()->endOfDay();
    }
// ================= SALES =================
$salesRaw = DB::table('orders')
    ->selectRaw("$groupSales as grp, SUM(netamount) as total")
    ->where('payment_status','paid')
    ->whereBetween('orderdate',[$start,$end])
    ->groupBy(DB::raw($groupSales))   // ✅ ถูกต้อง
    ->pluck('total','grp');



// ================= EXPENSE =================
$expenseRaw = DB::table('sales_detail')
    ->join('product','sales_detail.productid','=','product.productid')
    ->join('orders','sales_detail.orderid','=','orders.orderid')
    ->selectRaw("$groupExpense as grp,
        SUM(product.cost * sales_detail.quantity) as total")
    ->where('orders.payment_status','paid')
    ->whereBetween('orders.orderdate',[$start,$end])
    ->groupBy(DB::raw($groupExpense)) // ✅ ถูกต้อง
    ->pluck('total','grp');



    // ================= BUILD TIMELINE =================
    $labels  = [];
    $sales   = [];
    $expense = [];

   $current = $start->copy();

while ($current <= $end) {

    if ($period === 'year') {

        $key = $current->format('Y-m');
        $labels[] = $current->format('M');
        $current->addMonth();

    } else {

        $key = $current->format('Y-m-d');
        $labels[] = $current->format('d/m');
        $current->addDay();
    }

    $sales[]   = $salesRaw[$key]   ?? 0;
    $expense[] = $expenseRaw[$key] ?? 0;
}


    // ================= PROFIT =================
    $profit = [];
    foreach ($sales as $i => $value) {
        $profit[] = $value - ($expense[$i] ?? 0);
    }

    $totalIncome  = array_sum($sales);
    $totalExpense = array_sum($expense);
    $totalProfit  = $totalIncome - $totalExpense;

    // ================= PREVIOUS PERIOD =================
    $prevIncome = DB::table('orders')
        ->where('payment_status','paid')
        ->whereBetween('orderdate',[$prevStart,$prevEnd])
        ->sum('netamount');

    $prevExpense = DB::table('sales_detail')
        ->join('product','sales_detail.productid','=','product.productid')
        ->join('orders','sales_detail.orderid','=','orders.orderid')
        ->where('orders.payment_status','paid')
        ->whereBetween('orders.orderdate',[$prevStart,$prevEnd])
        ->sum(DB::raw('product.cost * sales_detail.quantity'));

    $prevProfit = $prevIncome - $prevExpense;

    $changePercent = 0;

    if ($prevProfit != 0) {
        $changePercent =
            (($totalProfit - $prevProfit) / abs($prevProfit)) * 100;
    }

    // ================= TOP PRODUCTS =================
    $topProducts = DB::table('sales_detail')
        ->join('product','sales_detail.productid','=','product.productid')
        ->join('orders','sales_detail.orderid','=','orders.orderid')
        ->select(
            'product.productname',
            DB::raw('SUM(sales_detail.quantity) as qty')
        )
        ->where('orders.payment_status','paid')
        ->whereBetween('orders.orderdate',[$start,$end])
        ->groupBy('product.productname')
        ->orderByDesc('qty')
        ->limit(3)
        ->get();

    return response()->json([
        'labels'  => $labels,
        'sales'   => $sales,
        'expense' => $expense,
        'profit'  => $profit,
        'income'  => $totalIncome,
        'outcome' => $totalExpense,
        'net'     => $totalProfit,
        'change_percent' => round($changePercent,2),
        'top'     => $topProducts
    ], 200, [], JSON_UNESCAPED_UNICODE);
}



public function summaryPage()
{
    return view('summary');
}







}
