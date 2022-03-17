<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class CartsController extends Controller {
    
    protected $viewPath = "pages.carts";


    public function index() {
        $currencySign = config("app.currency_sign");

        return $this->view("index", compact("currencySign"));
    }

}
