<?php

namespace App\Http\Controllers;

use App\Models\Post;

class UserPostsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Models\Post  $posts
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Post $posts, $id)
    {
        return response()->json(
            $posts->with('user', 'files')
                ->withCount('comments', 'likes')
                ->where('user_id', $id)
                ->latest()
                ->paginate(request('per_page'))
        );
    }
}
