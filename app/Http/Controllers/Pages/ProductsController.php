<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductsController extends Controller {
    
    protected $viewPath = "pages.products";


    public function index() {
        return $this->view("index");
    }

    public function show(Product $product) {
        $product = new ProductResource($product);

        return $this->view("show", compact("product"));
    }
}
