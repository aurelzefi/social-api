<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $users;

    /**
     * The post instance.
     *
     * @var \App\Models\Post
     */
    protected $posts;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\User  $users
     * @param  \App\Models\Post  $posts
     * @return void
     */
    public function __construct(User $users, Post $posts)
    {
        $this->users = $users;
        $this->posts = $posts;
    }

    /**
     * Display a listing of the search results.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = $this->users->where('name', 'like', '%'.request('query').'%')
                            ->orWhere('email', 'like',  '%'.request('query').'%')
                            ->get();

        $posts = $this->posts->with('user','files')
                            ->withCount('comments', 'likes')
                            ->where('content', 'like', '%'.request('query').'%')
                            ->get();

        return response()->json(compact('users', 'posts'));
    }

    /**
     * Display a listing of the user search results.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function users()
    {
        return response()->json(
            $this->users->where('name', 'like', '%'.request('query').'%')
                        ->orWhere('email', 'like',  '%'.request('query').'%')
                        ->limit(request('limit'))
                        ->get()
        );
    }
}
