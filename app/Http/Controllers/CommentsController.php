<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class CommentsController extends Controller
{
    /**
     * The comments implementation.
     *
     * @var \App\Models\Comment
     */
    protected $comments;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Comment  $comments
     * @return void
     */
    public function __construct(Comment $comments)
    {
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
            'content' => 'nullable|string',
        ]);

        $comment = request()->user()->comments()->create([
            'post_id' => request('post_id'),
            'content' => request('content'),
        ]);

        if (request()->hasFile('files')) {
            $comment->files()->createMany($this->getUploadedFiles());
        }
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $comment = $this->comments->findOrFail($id);

        $this->authorize('update', $comment);

        request()->validate([
            'content' => 'required|string',
        ]);

        $comment->fill([
            'content' => request('content'),
        ])->save();
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
        $comment = $this->comments->findOrFail($id);

        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json('', 204);
    }
}
