<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Notifications\PostLiked;

class LikesController extends Controller
{
    /**
     * Like the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($id)
    {
        request()->merge(compact('id'))->validate([
            'id' => [
                'required',
                'exists:posts',
                Rule::unique('likes', 'post_id')
                    ->where('user_id', request()->user()->id),
            ],
        ]);

        request()->user()->likes()->attach($id);

        $post = request()->user()->likes()->find($id);

        if ($post->user->id !== request()->user()->id) {
            $post->user->notify(new PostLiked(request()->user()));
        }

        return response()->json($post->makeHidden('user'));
    }

    /**
     * Unlike the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike($id)
    {
        request()->merge(compact('id'))->validate([
            'id' => 'required|exists:posts',
        ]);

        request()->user()->likes()->detach($id);

        return response()->json('', 204);
    }
}
