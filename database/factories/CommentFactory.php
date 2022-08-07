<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // user_id
        // post_id
        // content
        // likes
        // dislikes
        return [
            'content' => fake()->text(250),
            'likes' => fake()->numberBetween(0,20),
            'dislikes' => fake()->numberBetween(0,20),
            'created_at' => now(),
            'updated_at' => now(),

            'user_id' => User::factory()->create()->id,
            'post_id' => Post::factory()->create()->id,
        ];
    }
}
