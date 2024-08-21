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
    Route::controller(BookingController::class)->middleware('can:manage,App\Models\Booking')->prefix('booking')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(CustomerController::class)->middleware('can:manage,App\Models\Customer')->prefix('customer')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(InvoiceController::class)->middleware('can:manage,App\Models\Invoice')->prefix('invoice')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');

        // Invoices Products
        Route::get('/detail/{id}', 'orderDetails');
        Route::post('/order/{id}', 'order');
    });

    Route::controller(PermissionController::class)->middleware('can:manage,App\Models\Permission')->prefix('permission')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(ProductController::class)->middleware('can:manage,App\Models\Product')->prefix('product')->group(function () {
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

    Route::controller(RoomController::class)->middleware('can:manage,App\Models\Room')->prefix('room')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(SongController::class)->middleware('can:manage,App\Models\Song')->prefix('song')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(StaffController::class)->middleware('can:manage,App\Models\Staff')->prefix('staff')->group(function () {
        Route::get('/', 'listAll');
        Route::post('/', 'create');
        Route::get('/{id}', 'getDetails');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::controller(AuthController::class)->middleware('can:manage,App\Models\User')->prefix('auth')->group(function  () {
        Route::get('/info', 'info');
        Route::get('/all', 'show');
        Route::post('/logout', 'logout');
        Route::post('/role', 'decentralize');
    });

    Route::controller(RoleController::class)->middleware('can:manage,App\Models\Role')->prefix('role')->group(function () {
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
