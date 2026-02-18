<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckOwner
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('employeeid')) {
    return redirect('/login');
}

if (Session::get('role') !== 'เจ้าของ') {
    return redirect('/dashboard')
        ->with('error', 'เฉพาะเจ้าของเท่านั้น');
}


        return $next($request);
    }
}
