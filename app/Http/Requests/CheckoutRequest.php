<?php

namespace App\Http\Requests;

class CheckoutRequest extends RestFormRequest {

    protected function getPostRules(): Array{
        return [
            "items"        => ["required", "array", "min:1"],
            "items.*"        => ["required", "array", "min:1"],
        ];
    }

    protected function getPostKeys(): Array{
        return ["name", "description", "price", "stock", "image"];
    }

}
