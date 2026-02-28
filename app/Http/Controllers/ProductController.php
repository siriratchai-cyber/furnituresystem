<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        $query = DB::table('product');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('productid', 'like', '%' . $request->search . '%')
                    ->orWhere('productname', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->category) {
            $query->where('categories', $request->category);
        }
        if ($request->producttype) {
            $query->where('producttype', $request->producttype);
        }

        if ($request->sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('productid', 'asc');
        }

        $products = $query->get();

        $categories = DB::table('product')
            ->select('categories')
            ->distinct()
            ->get();

        $producttype = DB::table('product')
            ->select('producttype')
            ->distinct()
            ->get();

        return view('Product.list', compact('products', 'categories', 'producttype'));
    }

    public function create()
    {
        $suppliers = DB::table('supplier')->get();

        $categories = DB::table('product')
            ->select('categories')
            ->distinct()
            ->get();

        $woodtypes = DB::table('product')
            ->select('woodtype')
            ->distinct()
            ->get();

        $producttypes = DB::table('product')
            ->select('producttype')
            ->distinct()
            ->get();

        return view(
            'Product.form',
            compact('suppliers', 'categories', 'woodtypes', 'producttypes')
        );
    }

    public function edit($id)
    {
        $product = DB::table('product')
            ->where('productid', $id)
            ->first();

        $suppliers = DB::table('supplier')->get();

        $categories = DB::table('product')
            ->select('categories')
            ->distinct()
            ->get();

        $woodtypes = DB::table('product')
            ->select('woodtype')
            ->distinct()
            ->get();

        $producttypes = DB::table('product')
            ->select('producttype')
            ->distinct()
            ->get();

        return view(
            'Product.form',
            compact('product', 'suppliers', 'categories', 'woodtypes', 'producttypes')
        );
    }

    
    
    public function store(Request $request)
    {
        $last = DB::table('product')
        ->select(DB::raw('TRIM(productid) as productid'))
        ->orderBy('productid', 'desc')
        ->first();

        if ($last && preg_match('/P(\d+)/', $last->productid, $match)) {
            $nextNumber = (int)$match[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $newSupplierId = 'P' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        DB::table('product')->insert([
            'productid' => $newSupplierId,
            'productname' => $request->productname,
            'producttype' => $request->producttype,
            'supplierid' => $request->supplierid,
            'categories' => $request->categories,
            'woodtype' => $request->woodtype,
            'received_at' => now(),
            'cost' => $request->cost,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect('/products');
    }

    public function update(Request $request, $id)
    {
        DB::table('product')
            ->where('productid', $id)
            ->update([
                'productname' => $request->productname,
                'producttype' => $request->producttype,
                'supplierid' => $request->supplierid,
                'categories' => $request->categories,
                'woodtype' => $request->woodtype,
                'cost' => $request->cost,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

        return redirect('/products');
    }

    public function delete($id)
    {
        DB::table('product')
            ->where('productid', $id)
            ->delete();

        return redirect('/products');
    }
}