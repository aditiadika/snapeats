<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Table::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'entity_id' => fake()->word(),
            'branch_id' => fake()->word(),
            'table_number' => fake()->word(),
            'qr_code' => fake()->word(),
            'capacity' => fake()->numberBetween(-10000, 10000),
            'is_available' => fake()->boolean(),
        ];
    }
}
