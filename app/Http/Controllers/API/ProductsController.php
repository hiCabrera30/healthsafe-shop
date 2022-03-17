<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Uploads\ImageUploadService;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller {

    public function index(Request $request) {
        try {
            $products = Product::query();

            if ($request->has("ids")) {
                $products->whereIn("id", is_array($request->ids) ? $request->ids : json_decode($request->ids));
            }

            $products = $request->has("paginate") && !filter_var($request->paginate, FILTER_VALIDATE_BOOLEAN)
                ? $this->resolveResource($products->get(), ProductResource::class)->data
                : $this->paginateBuilder($products, ProductResource::class, 5);

            return $this->resolve("res.fetch.success", compact("products"));
        } catch (Exception $ex) {
            return $this->reject($ex, "res.fetch.failed");
        }
    }

    public function store(ProductRequest $request) {
        try {
            $data          = $request->getFormData();
            $data["image"] = ImageUploadService::resolveImage($data["image"], asset("images/default-product.jpeg"), "product");

            $product = Product::create($data);
            $product = new ProductResource($product);

            return $this->resolve("res.products.create.success", compact("product"));
        } catch (Exception $ex) {
            return $this->reject($ex, "res.products.create.failed");
        }
    }

    public function update(ProductRequest $request, Product $product) {
        try {
            $product = tap($product)->update($request->getFormData());
            $product = new ProductResource($product);

            return $this->resolve("res.products.update.success", compact("product"));
        } catch (Exception $ex) {
            return $this->reject($ex, "res.products.update.failed");
        }
    }

    public function updateImage(ProductRequest $request, Product $product) {
        try {
            $image   = ImageUploadService::upload($request->image, "product");
            $product = tap($product)->update(compact("image"));
            $product = new ProductResource($product);

            return $this->resolve("res.products.update-image.success", compact("product"));
        } catch (Exception $ex) {
            return $this->reject($ex, "res.products.update-image.failed");
        }
    }

    public function destroy(Product $product) {
        try {
            $product = tap($product)->delete();
            $product = new ProductResource($product);

            return $this->resolve("res.products.delete.success", compact("product"));
        } catch (Exception $ex) {
            return $this->reject($ex, "res.products.delete.failed");
        }
    }
}
