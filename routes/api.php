<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StaffController;

Route::controller(BookingController::class)->prefix('booking')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(CustomerController::class)->prefix('customer')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(InvoiceController::class)->prefix('invoice')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(ProductController::class)->prefix('product')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(RoomController::class)->prefix('room')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(SongController::class)->prefix('song')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(StaffController::class)->prefix('staff')->group(function () {
    Route::get('/', 'listAll');
    Route::post('/', 'create');
    Route::get('/{id}', 'getDetails');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});