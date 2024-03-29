<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "id" => fake()->randomNumber(5),
            "type" => fake()->randomElement(["private", "group", "supergroup", "channel"]),
            "name" => fake()->name(),
            "phone" => fake()->phoneNumber(),
            "username" => fake()->userName(),
            "language_code" => fake()->languageCode(),
        ];
    }
}
