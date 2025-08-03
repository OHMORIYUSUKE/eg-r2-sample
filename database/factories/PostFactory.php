<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Create a post for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a short post.
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraph(),
        ]);
    }

    /**
     * Create a long post.
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->sentence(8),
            'content' => $this->faker->paragraphs(6, true),
        ]);
    }
}