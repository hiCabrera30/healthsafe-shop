<?php

use App\Http\Controllers\PagesController;
use App\Http\Controllers\Pages\CartsController;
use App\Http\Controllers\Pages\ProductsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::name("products.index")->get('/products', [ProductsController::class, 'index']);
Route::name("products.show")->get('/products/{product}', [ProductsController::class, 'show']);

Route::name("carts.index")->get('/cart', [CartsController::class, 'index']);
