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

    // ===== TODAY SALES =====
    $todaySales = DB::table('orders')
        ->whereDate('orderdate', today())
        ->where('payment_status', 'paid')
        ->sum('netamount');

    // ===== LOW STOCK =====
    $lowStock = DB::table('product')
        ->where('stock', '<=', 5)
        ->get();

    $critical = DB::table('product')
        ->where('stock', '<=', 3)
        ->get();

    // ===== CHART DATA =====
    $sales = DB::table('orders')
        ->whereDate('orderdate', today())
        ->where('payment_status', 'paid')
        ->selectRaw('EXTRACT(HOUR FROM orderdate) as hour, SUM(netamount) as total')
        ->groupByRaw('EXTRACT(HOUR FROM orderdate)')
        ->orderBy('hour')
        ->get();

    $labels = [];
    $data = [];

    foreach ($sales as $s) {
        $labels[] = $s->hour . ":00";
        $data[] = $s->total;
    }

    return response()->json([
        'summary' => $summary,
        'todaySales' => $todaySales,
        'lowStock' => $lowStock,
        'critical' => $critical,
        'chart' => [
            'labels' => $labels,
            'data' => $data
        ]
    ]);
}


}
