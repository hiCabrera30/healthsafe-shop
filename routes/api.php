<?php

use App\Http\Controllers\API\CartsController;
use App\Http\Controllers\API\ProductsController;
use Illuminate\Support\Facades\Route;

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

Route::name("products.index")->get('/products', [ProductsController::class, 'index']);
Route::name("products.store")->post('/products', [ProductsController::class, 'store']);
Route::name("products.destroy")->delete('/products/{product}', [ProductsController::class, 'destroy']);
Route::name("products.update")->put('/products/{product}', [ProductsController::class, 'update']);
Route::name("products.update-image")->patch('/products/{product}/update-image', [ProductsController::class, 'updateImage']);


Route::name("carts.checkout")->post('/carts/checkout', [CartsController::class, 'checkout']);
