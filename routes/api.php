<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/room', [RoomController::class, 'store'])->name('room.store');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::controller(RoomController::class)->group(function() {
    Route::get('/room', 'index')->name('room.index');
    Route::get('/room/{room}', 'show')->name('room.show');
    Route::put('/room/{room}', 'update')->name('room.update'); // ...
});

Route::controller(BookingController::class)->group(function() {
    Route::get('/booking', 'index')->name('booking.index');
    Route::get('/booking/{booking}', 'show')->name('booking.show');
//    Route::post('/booking', 'store')->name('booking.store');
    Route::put('/booking/{booking}', 'update')->name('booking.update'); // ...
});

Route::controller(CustomerController::class)->group(function() {
    Route::get('/customer', 'index')->name('customer.index');
    Route::get('/customer/{customer}', 'show')->name('customer.show');
//    Route::post('/customer', 'store')->name('customer.store');
    Route::put('/customer/{customer}', 'update')->name('customer.update'); // ...
});

Route::controller(PaymentController::class)->group(function() {
    Route::get('/payment', 'index')->name('payment.index');
    Route::get('/payment/{payment}', 'show')->name('payment.show');
//    Route::post('/payment', 'store')->name('payment.store');
    Route::put('/payment/{payment}', 'update')->name('payment.update'); // ...
});
