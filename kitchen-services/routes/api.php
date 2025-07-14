<?php

use App\Http\Controllers\KitchenOrderController;
use App\Http\Controllers\KitchenRecipesController;
use App\Http\Controllers\MetricsController;

Route::prefix('kitchen/orders')->controller(KitchenOrderController::class)->group(function () {
    Route::post('/', 'store');
    Route::post('/resend-waiting', 'resendWaitingDishes');
    Route::get('/grouped', 'getOrdersWithDishes');
    Route::get('/dishes', 'getOrderDishesByStatus');
    Route::get('/dishes/all', 'getAllDishes');
});

Route::prefix('kitchen')->controller(KitchenRecipesController::class)->group(function () {
    Route::get('/recipes', 'index');
});

Route::prefix('metrics')->controller(MetricsController::class)->group(function () {
    Route::get('/ingredients-used', 'mostUsedIngredients');
    Route::get('/recipes', 'topRecipes');
});
