<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\Post::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'content' => $faker->text,
    ];
});
