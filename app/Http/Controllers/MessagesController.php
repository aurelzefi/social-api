<?php

namespace App\Http\Controllers;

use App\Models\Message;

class MessagesController extends Controller
{
    /**
     * The messages implementation.
     *
     * @var \App\Models\Message
     */
    protected $messages;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\Message  $messages
     * @return void
     */
    public function __construct(Message $messages)
    {
        $this->messages = $messages;
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
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
        ]);

        $message = request()->user()->sentMessages()->create([
            'receiver_id' => request('receiver_id'),
            'content' => request('content'),
        ]);

        if (request()->hasFile('files')) {
            $message->files()->createMany($this->getUploadedFiles());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $message = $this->messages->findOrFail($id);

        $this->authorize('update', $message);

        request()->validate([
            'content' => 'nullable|string',
        ]);

        $message->fill([
            'content' => request('content'),
        ])->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $message = $this->messages->findOrFail($id);

        $this->authorize('delete', $message);

        $message->delete();

        return response()->json('', 204);
    }
}
