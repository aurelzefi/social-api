<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Notifications\UserFollowed;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 30)->create()->each(function ($follower) {
            for ($i = 0; $i < rand(1, 10); $i++) {
                $followee = User::inRandomOrder()->first();

                if (! $follower->hasFollowed($followee) && $follower->id !== $followee->id) {
                    $followee->followers()->attach($follower);
                    $followee->notify(new UserFollowed($follower));
                }
            }
        });
    }
}
