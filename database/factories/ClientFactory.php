<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            // ICE (Identifiant Commun de l'Entreprise) - Placeholder value
            'ice' => $this->faker->unique()->randomNumber(9),
            'phone' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'adress' => $this->faker->address(),
            'sector' => $this->faker->randomElement(['Tech', 'Finance', 'Retail', 'Healthcare']),
            // IMPORTANT: Fetch an existing User's ID
            'user_id' => User::factory(), // This uses the Factory Relationship syntax
        ];
    }
}
