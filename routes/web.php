<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IngredientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/ingredient', [IngredientController::class, 'index'])->name('ingredient.index');
    Route::get('/ingredient/orders', [IngredientController::class, 'orders'])->name('ingredient.orders');
    Route::get('/ingredient/orders/delivered', [IngredientController::class, 'getDeliveredOrders'])->name('ingredient.delivered_orders');
    Route::patch('/ingredient/deliver/{ingredient}', [IngredientController::class, 'deliver'])->name('ingredient.deliver');
});

require __DIR__.'/auth.php';
