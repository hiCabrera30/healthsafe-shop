<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartsController extends Controller {

    public function checkout(Request $request) {
        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $affectedRows = Product::where("id", $item["id"])
                    ->where("stock", ">=", $item["amount"])
                    ->decrement('stock', $item["amount"]);
                
                if ($affectedRows == 0) {
                    throw new Exception(sprintf("%s has insufficient stock", $item['name']));
                }
            }
            DB::commit();
            return $this->resolve("res.carts.checkout.success");
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->reject($ex, "res.carts.checkout.failed");
        }
    }
}
