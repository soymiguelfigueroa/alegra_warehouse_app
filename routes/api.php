<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('ingredients', [IngredientController::class, 'getIngredients']);
Route::get('ingredients/get_by_order', [IngredientController::class, 'getIngredientsByOrder']);
Route::get('ingredients/get_by_receipt', [IngredientController::class, 'getIngredientsByReceipt']);

Route::get('purchases', [PurchaseController::class, 'getPurchases']);
