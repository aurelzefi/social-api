<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Filesystem\Filesystem;

class PostsController extends Controller
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The post instance.
     *
     * @var \App\Models\Post
     */
    protected $posts;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $files
     * @param  \App\Models\Post  $posts
     * @return void
     */
    public function __construct(Filesystem $files, Post $posts)
    {
        $this->files = $files;
        $this->posts = $posts;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created post in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'content' => 'required_without:files|nullable|string',
            'files' => 'array',
            'files.*' => 'mimes:jpeg,png,bmp,gif,mp4,mov,ogg',
        ]);

        $post = request()->user()->posts()->create([
            'content' => request('content'),
        ]);

        $this->storeFilesFor($post);

        return response()->json(
            $this->posts->with('user', 'files')->withCount('comments', 'likes')->find($post->id)
        );
    }

    /**
     * Display the specified post.
     *
     * @param  int  $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified post in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id)
    {
        $post = $this->posts->with('files')->findOrFail($id);

        $this->authorize('update', $post);

        request()->validate([
            'content' => 'required_without_all:current_files,files|nullable|string',
            'current_files' => 'array',
            'current_files.*' => Rule::in($post->files->pluck('id')),
            'files' => 'array',
            'files.*' => 'mimes:jpeg,png,bmp,gif,mp4,mov,ogg',
        ]);

        $ids = $post->files->pluck('id')->diff(request('current_files'));

        $this->files->delete(
            $post->files->whereIn('id', $ids)->pluck('name')->toArray()
        );

        $post->files()->whereIn('id', $ids)->delete();

        $this->storeFilesFor($post);

        $post->fill([
            'content' => request('content'),
        ])->save();

        return response()->json(
            $this->posts->with('user', 'files')->withCount('comments', 'likes')->findOrFail($id)
        );
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $post = $this->posts->with('files', 'comments.files')->findOrFail($id);

        $this->authorize('delete', $post);

        $this->files->delete(
            $post->files->pluck('name')->toArray()
        );

        $this->files->delete(
            $post->comments->pluck('files')->flatten()->pluck('name')->toArray()
        );

        $post->delete();
        $post->files()->delete();

        $post->comments->each(function ($comment) {
            $comment->files()->delete();
        });

        return response()->json('', 204);
    }
}
