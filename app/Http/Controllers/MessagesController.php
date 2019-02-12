<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Filesystem\Filesystem;

class MessagesController extends Controller
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The message instance.
     *
     * @var \App\Models\Message
     */
    protected $messages;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $files
     * @param  \App\Models\Message  $messages
     * @return void
     */
    public function __construct(Filesystem $files, Message $messages)
    {
        $this->files = $files;
        $this->messages = $messages;
    }

    /**
     * Store a newly created message in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        request()->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required_without:files|nullable|string',
            'files' => 'array',
            'files.*' => 'mimes:jpeg,png,bmp,gif,mp4,mov,ogg',
        ]);

        $message = request()->user()->sentMessages()->create([
            'receiver_id' => request('receiver_id'),
            'content' => request('content'),
            'read_at' => request()->user()->id === (int) request('receiver_id') ? now() : null,
        ]);

        $this->storeFilesFor($message);

        $message->load('files');

        if (request()->user()->id !== (int) request('receiver_id')) {
            event(new MessageSent($message));
        }

        return response()->json($message);
    }

    /**
     * Update the specified message in storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id)
    {
        $message = $this->messages->with('files')->findOrFail($id);

        $this->authorize('update', $message);

        request()->validate([
            'content' => 'required_without_all:current_files,files|nullable|string',
            'current_files' => 'array',
            'current_files.*' => Rule::in($message->files->pluck('id')),
            'files' => 'array',
            'files.*' => 'mimes:jpeg,png,bmp,gif,mp4,mov,ogg',
        ]);

        $ids = $message->files->pluck('id')->diff(request('current_files'));

        $this->files->delete(
            $message->files->whereIn('id', $ids)->pluck('name')->toArray()
        );

        $message->files()->whereIn('id', $ids)->delete();

        $this->storeFilesFor($message);

        $message->fill([
            'content' => request('content'),
        ])->save();

        return response()->json(
            $this->messages->with('files')->findOrFail($id)
        );
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $message = $this->messages->with('files')->findOrFail($id);

        $this->authorize('delete', $message);

        $this->files->delete(
            $message->files->pluck('name')->toArray()
        );

        $message->delete();
        $message->files()->delete();

        return response()->json('', 204);
    }
}
