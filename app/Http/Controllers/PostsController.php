<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\Filesystem\Filesystem;

class PostsController extends Controller
{
    /**
     * The posts implementation.
     *
     * @var \App\Models\Post
     */
    protected $posts;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Post  $posts
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Post $posts, Filesystem $files)
    {
        $this->posts = $posts;
        $this->files = $files;
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
            'files.*' => 'file',
        ]);

        $post = request()->user()->posts()->create([
            'content' => request('content'),
        ]);

        if (request()->hasFile('files')) {
            $post->files()->createMany($this->getUploadedFiles());
        }
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
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id)
    {
        $post = $this->posts->findOrFail($id);

        $this->authorize('update', $post);

        request()->validate([
            'content' => 'required_without:files|nullable|string',
            'files' => 'array',
            'files.*' => 'file',
        ]);

        $post->fill([
            'content' => request('content'),
        ])->save();
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
        $post = $this->posts->findOrFail($id);

        $this->authorize('delete', $post);

        $post->delete();

        $post->files()->delete();

        $this->files->delete(
            $post->files()->pluck('name')->toArray()
        );

        return response()->json('', 204);
    }
}
