<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendingMachineController;
use App\Http\Controllers\VendingInventoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// V1 Vending API Routes
Route::prefix('v1')->group(function () {
    Route::prefix('vending')->group(function () {
        Route::get('/get/meds', [VendingInventoryController::class, 'getMeds']);
        Route::post('/dispense-meds', [VendingInventoryController::class, 'dispenseMeds']);
        
        // Machine operations
        Route::post('/generate-token', [VendingMachineController::class, 'generateToken']);
        Route::post('/machine-details', [VendingMachineController::class, 'getMachineDetails']);
        Route::post('/send-instruction', [VendingMachineController::class, 'sendInstructionToMachine']);
        Route::post('/check-status', [VendingMachineController::class, 'checkMachineStatus']);
        Route::post('/hard-reset', [VendingMachineController::class, 'machineHardReset']);
    });
});

// V2 Enhanced Vending API Routes
Route::prefix('v2')->group(function () {
    Route::prefix('vending')->group(function () {
        Route::get('/get/meds', [VendingInventoryController::class, 'getMeds']);
        Route::post('/dispense-meds', [VendingInventoryController::class, 'dispenseMeds']);
        
        // Machine operations
        Route::post('/generate-token', [VendingMachineController::class, 'generateToken']);
        Route::post('/machine-details', [VendingMachineController::class, 'getMachineDetails']);
        Route::post('/send-instruction', [VendingMachineController::class, 'sendInstructionToMachine']);
        Route::post('/check-status', [VendingMachineController::class, 'checkMachineStatus']);
        Route::post('/hard-reset', [VendingMachineController::class, 'machineHardReset']);
        
        // Inventory Management API
        Route::prefix('inventory')->group(function () {
            Route::get('/', [VendingInventoryController::class, 'index']);
            Route::post('/', [VendingInventoryController::class, 'store']);
            Route::get('/{id}', [VendingInventoryController::class, 'show']);
            Route::put('/{id}', [VendingInventoryController::class, 'update']);
            Route::delete('/{id}', [VendingInventoryController::class, 'destroy']);
        });
        
        // Machine Management API
        Route::prefix('machines')->group(function () {
            Route::get('/', [VendingMachineController::class, 'index']);
            Route::post('/', [VendingMachineController::class, 'store']);
            Route::get('/{id}', [VendingMachineController::class, 'show']);
            Route::put('/{id}', [VendingMachineController::class, 'update']);
            Route::delete('/{id}', [VendingMachineController::class, 'destroy']);
        });
    });
});
