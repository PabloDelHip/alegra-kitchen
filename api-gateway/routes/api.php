<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WarehouseIngredientsController;
use App\Http\Controllers\AgentController;


Route::middleware('throttle:60,1')->group(function () {

  Route::post('/login', [AuthController::class, 'login']);

  Route::middleware('validate.token')->group(function () {

    Route::prefix('agent')->group(function () {
        Route::get('/recommendations', [AgentController::class, 'recommendations']);
    });


      Route::prefix('orders')->group(function () {
          Route::post('/', [OrderController::class, 'store']);
          Route::get('/list', [OrderController::class, 'list']);
          Route::get('/list/all', [OrderController::class, 'listAll']);
          Route::get('/grouped', [OrderController::class, 'grouped']);
      });

      Route::prefix('warehouse')->group(function () {
          Route::get('/ingredients', [WarehouseIngredientsController::class, 'index']);
          Route::post('/ingredients/buy', [WarehouseIngredientsController::class, 'buy']);
          Route::get('/purchases', [WarehouseIngredientsController::class, 'purchase']);
      });

      Route::prefix('kitchen')->group(function () {
          Route::get('/recipes', [KitchenController::class, 'recipe']);
      });

      Route::prefix('metrics')->group(function () {
          Route::get('/ingredients-used', [MetricsController::class, 'mostUsedIngredients']);
          Route::get('/recipes', [MetricsController::class, 'topRecipes']);
      });

  });

});
