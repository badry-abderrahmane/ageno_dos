<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 5, 500); // Price between 5 and 500
        $minQty = $this->faker->numberBetween(1, 10);

        return [
            'name' => $this->faker->unique()->words(2, true) . ' Widget',
            'delivery_time' => $this->faker->numberBetween(1, 14),
            'supplier_price' => $this->faker->randomFloat(2, 1, $price * 0.9), // Less than final price
            'price' => $price,
            'min_qty' => $minQty,
            'max_qty' => $this->faker->numberBetween($minQty, 100),
            'note' => $this->faker->boolean(30) ? $this->faker->sentence() : null, // 30% chance of having a note
            'img' => $this->faker->boolean(50) ? 'product_' . $this->faker->numberBetween(1, 5) . '.jpg' : null,
            // IMPORTANT: Define the relationships using factories
            'product_category_id' => ProductCategory::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
