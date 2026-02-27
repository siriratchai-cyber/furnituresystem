<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = DB::table('employee')->orderBy('employeeid')->get();
        return view('employee.employee', compact('employees'));
    }

    public function create()
    {
        return view('employee.emp_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'empname'    => 'required|string|max:100',
            'position'   => 'nullable|string|max:100',
            'tel'        => 'nullable|string|max:20',
        ]);

        $last = DB::table('employee')
        ->select(DB::raw('TRIM(employeeid) as employeeid'))
        ->orderBy('employeeid', 'desc')
        ->first();

        if ($last && preg_match('/Y(\d+)/', $last->employeeid, $match)) {
            $nextNumber = (int)$match[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $newEmployeeId = 'Y' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        DB::table('employee')->insert([
            'employeeid' => $newEmployeeId,
            'empname'    => trim($request->empname),
            'position'   => $request->position,
            'tel'        => $request->tel,
            'password'   => bcrypt($request->NULL),
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'เพิ่มพนักงานสำเร็จ');
    }

    public function edit($id)
    {
        $employee = DB::table('employee')->where('employeeid', $id)->first();

        if (!$employee) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่พบพนักงาน');
        }

        return view('employee.emp_edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'empname'  => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'tel'      => 'nullable|string|max:20',
        ]);

        DB::table('employee')->where('employeeid', $id)->update([
            'empname'  => trim($request->empname),
            'position' => $request->position,
            'tel'      => $request->tel,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy($id)
    {
        $hasOrders = DB::table('orders')->where('employeeid', $id)->exists();

        if ($hasOrders) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่สามารถลบได้ เนื่องจากพนักงานมีข้อมูลคำสั่งซื้อผูกอยู่');
        }

        DB::table('employee')->where('employeeid', $id)->delete();

        return redirect()->route('employees.index')
            ->with('success', 'ลบพนักงานสำเร็จ');
    }
}