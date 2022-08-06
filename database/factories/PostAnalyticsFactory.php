<?php

namespace Database\Factories;

use App\Models\PostAnalytics;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostAnalytics>
 */
class PostAnalyticsFactory extends Factory
{

    protected $model = PostAnalytics::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->jobTitle(),
            'content' => fake()->text(),
            'user_id' => (new UserFactory)->create()->id,

            'analytics_views' => fake()->numberBetween(0, 200),
            'analytics_favourites' => fake()->numberBetween(0, 200),
            'analytics_dislikes' => fake()->numberBetween(0, 200),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
