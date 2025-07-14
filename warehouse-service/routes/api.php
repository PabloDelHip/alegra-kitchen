<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\PurchaseHistoryController;

Route::get('/ingredients', [IngredientController::class, 'index']);
Route::post('/ingredients/buy', [IngredientController::class, 'buy']);
Route::get('/purchases', [PurchaseHistoryController::class, 'index']);
