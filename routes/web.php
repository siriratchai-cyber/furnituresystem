<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Login Page
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {

    if (Session::has('employee_id')) {
        return redirect('/dashboard');
    }

    return view('login');

})->name('login');


/*
|--------------------------------------------------------------------------
| Login Process
|--------------------------------------------------------------------------
*/

Route::post('/login-process', function (Request $request) {

    $request->validate([
        'empname'  => 'required',
        'password' => 'required'
    ]);

    $employee = DB::table('employee')
        ->where('empname', $request->empname)
        ->first();

    if ($employee && Hash::check($request->password, $employee->password)) {

        Session::put('employee_id', $employee->employeeid);
        Session::put('employee_name', $employee->empname);
        Session::put('role', $employee->position);
        Session::put('tel', $employee->tel);

        return redirect()->route('dashboard');
    }

    return redirect()->route('login')
        ->with('error', 'ชื่อพนักงานหรือรหัสผ่านไม่ถูกต้อง');

})->name('login.process');   // ✅ เพิ่มบรรทัดนี้


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    if (!Session::has('employee_id')) {
        return redirect()->route('login');
    }

    return view('dashboard');

})->name('dashboard');


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::get('/logout', function () {

    Session::flush();
    return redirect()->route('login');

})->name('logout');


/*
|--------------------------------------------------------------------------
| ORDER (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware(['checklogin'])->group(function () {

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/create', [OrderController::class, 'create'])
        ->name('orders.create');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');
});
