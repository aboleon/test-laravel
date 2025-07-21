<?php

namespace Database\Factories;

use App\Models\Dictionnary;
use Illuminate\Database\Eloquent\Factories\Factory;

class DictionnaryFactory extends Factory
{

    protected $model = Dictionnary::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->unique()->numberBetween(1, 1000000),
            'slug' => $this->faker->unique()->slug,
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(['meta', 'simple', 'custom']),
        ];
    }
}
