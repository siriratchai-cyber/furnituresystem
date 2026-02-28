<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = DB::table('supplier')->orderBy('supplierid')->get();
        return view('supplier.supplier', compact('suppliers')); 
    }
    public function create()
    {
        return view('supplier.sup_create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'suppliername'  => 'required|string|max:100',
            'tel'           => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'contactperson' => 'nullable|string|max:100',
        ]);

        $last = DB::table('supplier')
        ->select(DB::raw('TRIM(supplierid) as supplierid'))
        ->orderBy('supplierid', 'desc')
        ->first();

        if ($last && preg_match('/SUP(\d+)/', $last->supplierid, $match)) {
            $nextNumber = (int)$match[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $newSupplierId = 'SUP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        DB::table('supplier')->insert([
            'supplierid'    => $newSupplierId,
            'suppliername'  => trim($request->suppliername),
            'tel'           => $request->tel,
            'address'       => $request->address,
            'contactperson' => $request->contactperson,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'เพิ่มซัพพลายเออร์สำเร็จ');
    }

    public function edit($id)
    {
        $supplier = DB::table('supplier')->where('supplierid', $id)->first();

        if (!$supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', 'ไม่พบรายการ');
        }

        return view('supplier.sup_edit', compact('supplier')); 
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'suppliername'  => 'required|string|max:100',
            'tel'           => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'contactperson' => 'nullable|string|max:100',
        ]);

        DB::table('supplier')->where('supplierid', $id)->update([
            'suppliername'  => trim($request->suppliername),
            'tel'           => $request->tel,
            'address'       => $request->address,
            'contactperson' => $request->contactperson,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'แก้ไขสำเร็จ');
    }

    public function destroy($id)
    {
        $hasSupplies = DB::table('supplies')->where('supplierid', $id)->exists();

        if ($hasSupplies) {
            return redirect()->route('suppliers.index')
                ->with('error', 'ไม่สามารถลบได้ เนื่องจากมีข้อมูลการจัดซื้อผูกอยู่');
        }

        DB::table('supplier')->where('supplierid', $id)->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'ลบรายการสำเร็จ');
    }
}