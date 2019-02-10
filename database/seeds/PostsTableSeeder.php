<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Notifications\PostLiked;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Post::class, 50)->create()->each(function ($post) {
            for ($i = 0; $i < rand(1, 10); $i++) {
                $user = User::inRandomOrder()->first();

                if (! $user->hasLiked($post)) {
                    $post->likes()->attach($user);

                    if ($user->id !== $post->user_id) {
                        $post->user->notify(new PostLiked($user));
                    }
                }
            }
        });
    }
}
