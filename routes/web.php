<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;

Route::get('/', [RentalController::class, 'index']);
Route::post('/items', [RentalController::class, 'storeItem'])->name('items.store');
Route::post('/rentals', [RentalController::class, 'storeRental'])->name('rentals.store');
Route::PATCH('/rentals/{id}/return', [RentalController::class, 'returnItem'])->name('rentals.return');