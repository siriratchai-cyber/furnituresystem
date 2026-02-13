<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // เพิ่มสินค้า
    public function store(Request $request)
    {
        DB::table('product')->insert([
            'productname' => $request->productname,
            'price'       => $request->price,
            'unit'        => $request->unit,
        ]);

        return redirect('/');
    }

    // แก้ไขสินค้า
    public function update(Request $request, $id)
    {
        DB::table('product')
            ->where('productid', $id)
            ->update([
                'productname' => $request->productname,
                'price'       => $request->price,
                'unit'        => $request->unit,
            ]);

        return redirect('/');
    }

    // ลบสินค้า
    public function delete($id)
    {
        DB::table('product')
            ->where('productid', $id)
            ->delete();

        return redirect('/');
    }
}
