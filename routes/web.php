<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendingMachineController;
use App\Http\Controllers\VendingInventoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Vending Machine Management Routes
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function() {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Vending Machine Routes
    Route::prefix('machines')->name('machines.')->group(function () {
        Route::get('/', [VendingMachineController::class, 'index'])->name('index');
        Route::get('/create', function() { return view('admin.machines.create'); })->name('create');
        Route::post('/', [VendingMachineController::class, 'store'])->name('store');
        Route::get('/{id}', [VendingMachineController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VendingMachineController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VendingMachineController::class, 'update'])->name('update');
        Route::delete('/{id}', [VendingMachineController::class, 'destroy'])->name('destroy');
    });
    
    // Inventory Management Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [VendingInventoryController::class, 'index'])->name('index');
        Route::get('/create', function() { return view('admin.inventory.create'); })->name('create');
        Route::post('/', [VendingInventoryController::class, 'store'])->name('store');
        Route::get('/{id}', [VendingInventoryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', function($id) { return view('admin.inventory.edit', compact('id')); })->name('edit');
        Route::put('/{id}', [VendingInventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [VendingInventoryController::class, 'destroy'])->name('destroy');
        
        // Reports
        Route::get('/reports/low-stock', [VendingInventoryController::class, 'lowStockReport'])->name('reports.low-stock');
        Route::get('/reports/expiry', [VendingInventoryController::class, 'expiryReport'])->name('reports.expiry');
    });
});
