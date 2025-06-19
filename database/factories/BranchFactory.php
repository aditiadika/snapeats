<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'entity_id' => fake()->word(),
            'name' => fake()->name(),
            'address' => fake()->text(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
