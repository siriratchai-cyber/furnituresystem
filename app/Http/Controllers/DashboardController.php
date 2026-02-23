<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
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

        $lowStock = DB::table('product')
            ->where('stock', '<=', 5)
            ->count();

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
        $summary = [
            'pending' => DB::table('orders')
                ->where('payment_status', 'pending')
                ->count(),

            'paid' => DB::table('orders')
                ->where('payment_status', 'paid')
                ->count(),
        ];

        $start = now()->startOfWeek();
        $end   = now()->endOfWeek()->endOfDay();

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
            $data[]   = $weeklySales[$dateKey]->total ?? 0;
        }

        return response()->json([
            'summary' => $summary,
            'todaySales' => DB::table('orders')
                ->whereDate('orderdate', today())
                ->where('payment_status', 'paid')
                ->sum('netamount'),

            'lowStock' => DB::table('product')
                ->where('stock', '<=', 5)
                 ->orderBy('stock', 'asc') 
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
            $step  = 'day';

            $prevStart = now()->subWeek()->startOfWeek();
            $prevEnd   = now()->subWeek()->endOfWeek()->endOfDay();

        }elseif ($period === 'month') {

    $start = now()->startOfMonth();
    $end   = now()->endOfMonth()->endOfDay();
    $step  = 'week';   // แก้ให้ใช้ week ที่มีใน while

    $prevStart = now()->subMonth()->startOfMonth();
    $prevEnd   = now()->subMonth()->endOfMonth()->endOfDay();
}else {

            $start = now()->startOfYear();
            $end   = now()->endOfYear()->endOfDay();
            $step  = 'month';

            $prevStart = now()->subYear()->startOfYear();
            $prevEnd   = now()->subYear()->endOfYear()->endOfDay();
        }

        $orders = DB::table('orders')
            ->where('payment_status','paid')
            ->whereBetween('orderdate',[$start,$end])
            ->get();

        $details = DB::table('sales_detail')
            ->join('product','sales_detail.productid','=','product.productid')
            ->join('orders','sales_detail.orderid','=','orders.orderid')
            ->where('orders.payment_status','paid')
            ->whereBetween('orders.orderdate',[$start,$end])
            ->select(
                'orders.orderdate',
                DB::raw('product.cost * sales_detail.quantity as cost_total')
            )
            ->get();

        $labels  = [];
        $sales   = [];
        $expense = [];

        $current = $start->copy();

        while ($current <= $end) {

            if ($step === 'day') {

                $rangeStart = $current->copy()->startOfDay();
                $rangeEnd   = $current->copy()->endOfDay();
                $label      = $current->format('d/m');
                $current->addDay();

            }  elseif ($step === 'week') {

    // สัปดาห์เริ่มจาก current จริง ๆ ไม่ใช้ startOfWeek()
    $rangeStart = $current->copy();

    // บวก 6 วันเพื่อให้ครบ 7 วัน
    $rangeEnd = $current->copy()->addDays(6);

    // กันเลยเดือน
    if ($rangeEnd->gt($end)) {
        $rangeEnd = $end->copy();
    }

    $label = $rangeStart->format('d/m') . ' - ' . $rangeEnd->format('d/m');

    // ขยับไปสัปดาห์ถัดไปแบบตรง ๆ
    $current->addDays(7);
            } else {

                $rangeStart = $current->copy()->startOfMonth();
                $rangeEnd   = $current->copy()->endOfMonth();
                $label      = $current->format('M');
                $current->addMonth();
            }

            $periodIncome = $orders
                ->filter(fn($o) =>
                    Carbon::parse($o->orderdate)->between($rangeStart, $rangeEnd)
                )
                ->sum('netamount');

            $periodExpense = $details
                ->filter(fn($d) =>
                    Carbon::parse($d->orderdate)->between($rangeStart, $rangeEnd)
                )
                ->sum('cost_total');

            $labels[]  = $label;
            $sales[]   = $periodIncome;
            $expense[] = $periodExpense;
        }

        $profit = [];
        foreach ($sales as $i => $value) {
            $profit[] = $value - ($expense[$i] ?? 0);
        }

        $totalIncome  = array_sum($sales);
        $totalExpense = array_sum($expense);
        $totalProfit  = $totalIncome - $totalExpense;

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

        $changePercent = $prevProfit == 0
            ? '-'
            : round((($totalProfit - $prevProfit) / abs($prevProfit)) * 100, 2);

        $topProducts = DB::table('sales_detail')
            ->join('product','sales_detail.productid','=','product.productid')
            ->join('orders','sales_detail.orderid','=','orders.orderid')
            ->where('orders.payment_status','paid')
            ->whereBetween('orders.orderdate',[$start,$end])
            ->select(
                'product.productname',
                DB::raw('SUM(sales_detail.quantity) as qty')
            )
            ->groupBy('product.productname')
            ->orderByDesc('qty')
            ->limit(3)
            ->get();

        return response()->json([
            'labels' => $labels,
            'sales'  => $sales,
            'expense'=> $expense,
            'profit' => $profit,
            'income' => $totalIncome,
            'outcome'=> $totalExpense,
            'net'    => $totalProfit,
            'change_percent' => $changePercent,
            'top' => $topProducts
        ]);
    }

    public function summaryPage()
    {
        return view('summary');
    }
}