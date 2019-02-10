<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\Message::class, function (Faker $faker) {
    return [
        'sender_id' => User::inRandomOrder()->first()->id,
        'receiver_id' => User::inRandomOrder()->first()->id,
        'content' => $faker->text,
    ];
});
