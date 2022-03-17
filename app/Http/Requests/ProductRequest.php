<?php

namespace App\Http\Requests;

class ProductRequest extends RestFormRequest {

    protected function getPostRules(): Array{
        return [
            "name"        => ["required"],
            "description" => [],
            "price"       => ["required", 'numeric'],
            "stock"       => ["required", 'numeric'],
        ];
    }

    protected function getPostKeys(): Array{
        return ["name", "description", "price", "stock", "image"];
    }

    protected function getPatchRules(): Array{
        return [
            "image"        => ["required"],
        ];
    }

    protected function getPatchKeys(): Array{
        return ["image"];
    }

    protected function getPutRules(): Array{
        return [
            "name"        => ["required"],
            "description" => [],
            "price"       => ["required", 'numeric'],
            "stock"       => ["required", 'numeric'],
        ];
    }

    protected function getPutKeys(): Array{
        return ["name", "description", "price", "stock"];
    }

}
