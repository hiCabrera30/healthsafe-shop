<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            "name"        => $this->faker->name(),
            "description" => $this->faker->paragraph(),
            "price"       => $this->faker->randomDigitNot(0) * $this->faker->randomDigitNot(0),
            "image"       => asset("images/default-product.jpeg"),
            "stock"       => $this->faker->randomDigitNot(0)
        ];
    }
}
