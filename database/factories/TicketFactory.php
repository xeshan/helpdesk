<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['billing','technical','account','sales','general'];
        return [
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(['open','in_progress','resolved','closed']),
            'category' => fake()->randomElement($categories),
            'explanation' => fake()->boolean(60) ? fake()->sentence(12) : null,
            'confidence' => fake()->boolean(60) ? fake()->randomFloat(2, 0.4, 0.99) : null,
            'note' => fake()->boolean(40) ? fake()->sentences(2, true) : null,
            'category_source' => fake()->boolean(20) ? 'manual' : 'ai',
        ];
    }
}
