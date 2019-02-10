<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Validation\Rule;
use App\Notifications\PostCommented;
use Illuminate\Contracts\Filesystem\Filesystem;

class CommentsController extends Controller
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The comment instance.
     *
     * @var \App\Models\Comment
     */
    protected $comments;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $files
     * @param  \App\Models\Comment  $comments
     * @return void
     */
    public function __construct(Filesystem $files, Comment $comments)
    {
        $this->files = $files;
        $this->comments = $comments;
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
     * Store a newly created comment in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required_without:files|nullable|string',
            'files' => 'array',
            'files.*' => 'mimes:jpeg,png,bmp,gif,mp4,mov,ogg',
        ]);

        $comment = request()->user()->comments()->create([
            'post_id' => request('post_id'),
            'content' => request('content'),
        ]);

        $user = $comment->post->user;

        if ($user->id !== request()->user()->id) {
            $user->notify(new PostCommented($comment->load('user')));
        }

        $this->storeFilesFor($comment);

        return response()->json($comment->makeHidden('post'));
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id)
    {
        $comment = $this->comments->with('files')->findOrFail($id);

        $this->authorize('update', $comment);

        request()->validate([
            'content' => 'required_without_all:current_files,files|nullable|string',
            'current_files' => 'array',
            'current_files.*' => Rule::in($comment->files->pluck('id')),
            'files' => 'array',
            'files.*' => 'mimes:jpeg,png,bmp,gif,mp4,mov,ogg',
        ]);

        $ids = $comment->files->pluck('id')->diff(request('current_files'));

        $this->files->delete(
            $comment->files->whereIn('id', $ids)->pluck('name')->toArray()
        );

        $comment->files()->whereIn('id', $ids)->delete();

        $this->storeFilesFor($comment);

        $comment->fill([
            'content' => request('content'),
        ])->save();

        return response()->json(
            $this->comments->with('user', 'files')->findOrFail($id)
        );
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $comment = $this->comments->with('files')->findOrFail($id);

        $this->authorize('delete', $comment);

        $this->files->delete(
            $comment->files->pluck('name')->toArray()
        );

        $comment->delete();
        $comment->files()->delete();

        return response()->json('', 204);
    }
}
