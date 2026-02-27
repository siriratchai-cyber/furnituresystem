<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /* ===============================
       LOGIN PAGE
    =============================== */
    public function showLogin()
    {
        return view('login');
    }

    public function showSupplier()
    {
        return view('supplier');
    }

    /* ===============================
       LOGIN PROCESS
    =============================== */
    public function login(Request $request)
    {
        $request->validate([
            'empname' => 'required',
            'password' => 'required'
        ]);

        $employee = DB::table('employee')
            ->where('empname', $request->empname)
            ->first();

        if (!$employee || !$employee->password || 
    !Hash::check($request->password, $employee->password)) {

    return back()->with('error','ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
}


        Session::put('employeeid', $employee->employeeid);
Session::put('employee_name', $employee->empname); // แก้ชื่อ key
Session::put('role', $employee->position);
Session::put('tel', $employee->tel); // เพิ่มบรรทัดนี้

        return redirect('/dashboard');
    }

    /* ===============================
       FORGOT PASSWORD PAGE
    =============================== */
    public function showForgotForm()
    {
        return view('forgot');
    }

    /* ===============================
       RESET PASSWORD
    =============================== */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'empname' => 'required',
            'tel' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $employee = DB::table('employee')
            ->where('empname',$request->empname)
            ->where('tel',$request->tel)
            ->first();

        if(!$employee){
            return back()->with('error','ข้อมูลไม่ถูกต้อง');
        }

        DB::table('employee')
            ->where('employeeid',$employee->employeeid)
            ->update([
                'password'=>Hash::make($request->password)
            ]);

        return back()->with('success','รีเซ็ตรหัสผ่านสำเร็จ');
    }

    /* ===============================
       LOGOUT
    =============================== */
    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }

    /* ===============================
   REGISTER PAGE
================================ */
public function showRegister()
{
    return view('register');
}

/* ===============================
   REGISTER PROCESS
================================ */
public function register(Request $request)
{
    $request->validate([
        'empname'  => 'required',
        'tel'      => 'required',
        'password' => 'required|min:6|confirmed'
    ]);

    // 🔎 ตรวจสอบว่าพนักงานมีอยู่ในระบบแล้วหรือไม่
    $employee = DB::table('employee')
        ->where('empname', $request->empname)
        ->where('tel', $request->tel)
        ->first();

    if (!$employee) {
        return back()->with('error', 'ไม่พบข้อมูลพนักงานในระบบ');
    }

    // ❗ ถ้ามี password แล้ว แปลว่าเคย activate แล้ว
    if ($employee->password !== null) {
        return back()->with('error', 'บัญชีนี้ถูกตั้งรหัสผ่านแล้ว');
    }

    // 🔐 Hash password แล้วอัปเดต
    DB::table('employee')
        ->where('employeeid', $employee->employeeid)
        ->update([
            'password' => Hash::make($request->password)
        ]);

    return redirect('/login')
        ->with('success', 'ตั้งรหัสผ่านสำเร็จ สามารถเข้าสู่ระบบได้');
}

}