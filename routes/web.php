<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('login.process');

Route::get('/forgot-password', [AuthController::class,'showForgotForm'])
    ->name('password.forgot');


Route::post('/forgot-password', [AuthController::class,'resetPassword'])
    ->name('password.reset');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('check.login')
    ->name('dashboard');



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('orders.index');
});


/*
|--------------------------------------------------------------------------
| Orders Module
|--------------------------------------------------------------------------
*/

Route::prefix('orders')
    ->name('orders.')
    ->middleware('check.login')
    ->group(function () {

        Route::get('/', [OrderController::class, 'index'])->name('index');

        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');

        Route::get('/live-search', [OrderController::class, 'liveSearch'])->name('liveSearch');
        Route::get('/filter', [OrderController::class, 'filter'])->name('filter');

        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('cancel');

        Route::post('/{id}/pay', [OrderController::class, 'pay'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('pay');

        Route::get('/{id}/receipt', [OrderController::class, 'receipt'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('receipt');

        Route::get('/{id}/tax-invoice', [OrderController::class, 'tax'])
    ->where('id', '[A-Za-z0-9]+')
    ->name('tax');


        Route::get('/{id}', [OrderController::class, 'show'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('show');

        Route::put('/{id}', [OrderController::class, 'update'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('update');

        Route::delete('/{id}', [OrderController::class, 'destroy'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('destroy');
    });
Route::middleware('check.owner')->group(function () {
    Route::get('/owner-dashboard', [DashboardController::class, 'owner']);
});

Route::get('/dashboard/data', [App\Http\Controllers\DashboardController::class, 'data'])
    ->name('dashboard.data')
    ->middleware('check.login');

Route::get('/register',[AuthController::class,'showRegister'])
    ->name('register');

Route::post('/register',[AuthController::class,'register'])
    ->name('register.process');


Route::get('/sales-summary', [DashboardController::class, 'summaryPage'])
    ->name('sales.summary')
    ->middleware(['check.login','check.owner']);


Route::get('/sales-summary-data', [DashboardController::class, 'salesSummaryData'])
    ->name('sales.summary.data')
    ->middleware(['check.login','check.owner']);




