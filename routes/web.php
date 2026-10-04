<?php

use App\Http\Controllers\BreedingEventController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeedingLogController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\OffspringBatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\WaterLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Livestock & Farm Tanks
    Route::resource('species', SpeciesController::class);
    
    // Tank QR Scanner & Lookup (defined before resource to avoid wildcard collision)
    Route::get('tanks/scan', [TankController::class, 'scan'])->name('tanks.scan');
    Route::get('tanks/lookup/{code}', [TankController::class, 'lookup'])->name('tanks.lookup');
    Route::get('tanks/{tank}/print-label', [TankController::class, 'printLabel'])->name('tanks.print-label');
    Route::resource('tanks', TankController::class);

    Route::resource('livestock', LivestockController::class);
    Route::resource('batches', OffspringBatchController::class);
    Route::post('batches/{batch}/logs', [OffspringBatchController::class, 'recordLog'])->name('batches.record-log');
    Route::resource('breeding', BreedingEventController::class);

    // Husbandry
    Route::resource('water-logs', WaterLogController::class);
    Route::resource('feeding', FeedingLogController::class);
    Route::resource('maintenance', MaintenanceLogController::class);

    // Business
    Route::resource('inventory', InventoryItemController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('expenses', ExpenseController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
