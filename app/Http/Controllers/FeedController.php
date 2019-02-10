<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FeedController extends Controller
{
    /**
     * The post model instance.
     *
     * @var \App\Models\Post
    */
    protected $posts;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Post  $posts
     * @return void
     */
    public function __construct(Post $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Display a listing of the feed posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
}
