<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RoleController;

use App\Http\Middleware\InventoryManageAccept;

Route::middleware('auth:sanctum')->group(function () {
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

        // Invoices Products
        Route::get('/detail/{id}', 'orderDetails');
        Route::post('/order/{id}', 'order');
    });

    Route::controller(PermissionController::class)->prefix('permission')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(ProductController::class)->prefix('product')->group(function () {
        // Stock management start
        Route::middleware(InventoryManageAccept::class)->group(function () {
            Route::get('/stock', 'getProductsAlert');
            Route::post('/stock', 'importProducts');
        });
        // Stock management end

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

    Route::controller(AuthController::class)->prefix('auth')->group(function  () {
        Route::get('/info', 'info');
        Route::get('/all', 'show');
        Route::post('/logout', 'logout');
        Route::post('/role', 'decentralize');
    });

    Route::controller(RoleController::class)->prefix('role')->group(function () {
        // Decentralize permissions for role start
        Route::get('/permission', 'showPermissions');
        Route::post('/permission', 'decentralize');
        // Decentralize permissions for role end

        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});