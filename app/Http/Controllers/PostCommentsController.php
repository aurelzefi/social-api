<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostCommentsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Models\Post  $posts
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Post $posts, $id)
    {
        return response()->json(
            $posts->with('comments.user', 'comments.files')->findOrFail($id)->comments
        );
    }
}
