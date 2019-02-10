<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FeedController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Models\Post  $posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Post $posts)
    {
        return response()->json(
            $posts->with('user','files')
                ->withCount('comments', 'likes')
                ->where('user_id', request()->user()->id)
                ->orWhereIn('user_id', function ($query) {
                    $query->select('followee_id')
                        ->from('followings')
                        ->where('follower_id', request()->user()->id);
                })
                ->latest()
                ->paginate(request('per_page'))
        );
    }
}
