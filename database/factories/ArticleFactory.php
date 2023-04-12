<?php

namespace Database\Factories;

use App\Article;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1000,
            'slug' => 'slug_' . Str::random(8),
            'cover' => 'img_url',
            'heading' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(4),
            'tags' => 'Instagram',
            'headtitle' => $this->faker->sentence(2),
            'headdescription' => $this->faker->sentence(4),
            'article' => $this->faker->sentence(12),
            'views' => rand(0, 100),
        ];
    }
}
