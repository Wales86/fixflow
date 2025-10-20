<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InternalNoteController;
use App\Http\Controllers\RepairOrderController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    Route::get('repair-orders', [RepairOrderController::class, 'index'])->name('repair-orders.index');
    Route::get('repair-orders/create', [RepairOrderController::class, 'create'])->name('repair-orders.create');
    Route::post('repair-orders', [RepairOrderController::class, 'store'])->name('repair-orders.store');
    Route::get('repair-orders/{repairOrder}', [RepairOrderController::class, 'show'])->name('repair-orders.show');
    Route::get('repair-orders/{repairOrder}/edit', [RepairOrderController::class, 'edit'])->name('repair-orders.edit');
    Route::patch('repair-orders/{repairOrder}', [RepairOrderController::class, 'update'])->name('repair-orders.update');
    Route::patch('repair-orders/{repairOrder}/status', [RepairOrderController::class, 'updateStatus'])->name('repair-orders.update-status');
    Route::delete('repair-orders/{repairOrder}', [RepairOrderController::class, 'destroy'])->name('repair-orders.destroy');

    Route::post('internal-notes', [InternalNoteController::class, 'store'])->name('internal-notes.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
