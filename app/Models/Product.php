<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    public $fillable = ["name", "description", "price", "image", "stock"];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function getDisplayedPriceAttribute() {
        return sprintf("%s %s", config("app.currency_sign"), $this->price);
    }

}
