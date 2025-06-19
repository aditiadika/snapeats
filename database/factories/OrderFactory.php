<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'entity_id' => fake()->word(),
            'branch_id' => fake()->word(),
            'table_id' => fake()->word(),
            'customer_name' => fake()->word(),
            'status' => fake()->word(),
            'total_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'notes' => fake()->text(),
        ];
    }
}
