<?php

use App\Models\Comment;
use Illuminate\Database\Seeder;
use App\Notifications\PostCommented;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Comment::class, 150)->create()->each(function ($comment) {
            $user = $comment->post->user;

            if ($user->id !== $comment->user_id) {
                $user->notify(new PostCommented($comment->load('user')));
            }
        });
    }
}
