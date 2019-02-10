<?php

use App\Models\Post;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\Comment::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'post_id' => Post::inRandomOrder()->first()->id,
        'content' => $faker->text,
    ];
});
