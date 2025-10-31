<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total' => $this->faker->randomFloat(2, 50, 5000),
            'status' => $this->faker->randomElement(['not_paid', 'paid', 'partially_paid', 'cancelled']),
            // IMPORTANT: Define the relationships using factories
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
        ];
    }
}
