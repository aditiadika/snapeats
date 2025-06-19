<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => fake()->word(),
            'product_id' => fake()->word(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'unit_price' => fake()->randomFloat(2, 0, 99999999.99),
            'notes' => fake()->text(),
        ];
    }
}
