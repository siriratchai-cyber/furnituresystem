<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function list()
    {
        $products = DB::table('product')->get();
        return view('Product.list', compact('products'));
    }

    public function edit($id)
    {
        $product = DB::table('product')
            ->where('productid', $id)
            ->first();

        return view('Product.form', compact('product'));
    }

    // เพิ่มสินค้า
    public function store(Request $request)
    {
        DB::table('product')->insert([
            'productid' => $request->productid,
            'productname' => $request->productname,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect('/products');
    }

    // แก้ไขสินค้า
    public function update(Request $request, $id)
    {
        DB::table('product')
            ->where('productid', $id)
            ->update([
                'productname' => $request->productname,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

        return redirect('/products');
    }

    // ลบสินค้า
    public function delete($id)
    {
        DB::table('product')
            ->where('productid', $id)
            ->delete();

        return redirect('/products');
    }
}