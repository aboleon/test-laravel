<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DictionnaryEntry>
 */
class DictionnaryEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'dictionnary_id' => fake()->randomDigitNotNull(),
            'parent' => null,
            'position' => fake()->randomDigitNotNull(),
            'custom' => null
        ];
    }
}
