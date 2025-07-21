<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{

    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deleted_at' => null,
            'published' => 1,
            'starts' => fake()->dateTime()->format('d/m/Y'),
            'sub_starts' => fake()->dateTimeBetween('now','+1 day')->format('d/m/Y'),
            'sub_ends' => fake()->dateTimeBetween('now','+1 month')->format('d/m/Y'),
            'event_main_id' => fake()->randomDigitNotNull(),
            'event_type_id' => fake()->randomDigitNotNull(),
            'place_id' => fake()->randomDigitNotNull(),
            'bank_account_id' => fake()->randomDigitNotNull(),
            'admin_id' => fake()->randomDigitNotNull(),
            'subs_admin_id' => fake()->randomDigitNotNull(),
            'has_abstract' => 1,
            'has_external_accommodation' => null,
            'reminder_unpaid_accommodation' => fake()->numberBetween(1,7),
            'created_by' => fake()->randomDigitNotNull,
            'bank_card_code' => Str::random(3),
            'transport_tickets_limit_date' => fake()->dateTimeBetween('+3 days','+2 months')->format('d/m/Y'),
            'flags' => 'fr'
        ];
    }
}
