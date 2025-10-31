<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\ProductCategory;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create essential dependencies (Users, Suppliers, Categories)
        // Ensure you have at least 1 User for Client/Invoice foreign keys
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test' . rand(0, 90091) . '@example.com',
        ]);

        // Create 20 Suppliers and 10 Product Categories
        Supplier::factory()->count(20)->create();
        ProductCategory::factory()->count(10)->create();

        // 2. Create Clients and Products
        // The Client factory will automatically create a User if one doesn't exist
        Client::factory()->count(50)->create();

        // The Product factory will automatically create a Supplier and ProductCategory if one doesn't exist
        Product::factory()->count(100)->create();

        // 3. Create Invoices and attach Products (Handling the Pivot Table)
        Invoice::factory()
            ->count(30)
            ->create()
            ->each(function ($invoice) {
                // Get a random selection of 1 to 5 products
                $products = Product::inRandomOrder()->take(rand(1, 5))->get();

                // Build the data array for the pivot table (invoice_products)
                $pivotData = [];
                foreach ($products as $product) {
                    $qty = rand(1, 10);
                    $price = $product->price; // Use the product's price from the factory

                    $pivotData[$product->id] = [
                        'qty' => $qty,
                        'price' => $price,
                        // Note: total is calculated in the model or service layer,
                        // but we need the qty and price for the pivot table.
                    ];
                }

                // Attach the products to the invoice using the pivot data
                $invoice->products()->attach($pivotData);

                // OPTIONAL: Update the invoice total based on the attached products
                $total = collect($pivotData)->sum(fn($item) => $item['qty'] * $item['price']);
                $invoice->update(['total' => $total]);
            });
    }
}
