<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationsController extends Controller
{
    /**
     * The message instance.
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
     * Display a listing of the conversations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            $this->messages->with('sender', 'receiver')
                ->where(function ($query) {
                    $query->where('sender_id', request()->user()->id)
                        ->orWhere('receiver_id', request()->user()->id);
                })
                ->whereIn(
                    'id',
                    $this->messages->selectRaw('max(id) id')
                        ->groupBy(
                            DB::raw(
                                'if(sender_id > receiver_id, 
                                    concat(sender_id, receiver_id), 
                                    concat(receiver_id, sender_id)
                                )'
                            )
                        )
                )
                ->latest()
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the last message with the given user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json(
            $this->messages->with('sender', 'receiver')
                ->where(function ($query) use ($id) {
                    $query->where('receiver_id', $id)
                        ->where('sender_id', request()->user()->id);
                })
                ->orWhere(function ($query) use ($id) {
                    $query->where('sender_id', $id)
                        ->where('receiver_id', request()->user()->id);
                })
                ->latest()
                ->first()
        );
    }

    /**
     * Update the messages with the given user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $this->messages
            ->where(function ($query) use ($id) {
                $query
                    ->where(function ($query) use ($id) {
                        $query->where('receiver_id', $id)
                            ->where('sender_id', request()->user()->id);
                    })
                    ->orWhere(function ($query) use ($id) {
                        $query->where('sender_id', $id)
                            ->where('receiver_id', request()->user()->id);
                    });
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(
            $this->messages->with('files')
                ->where(function ($query) use ($id) {
                    $query->where('receiver_id', $id)
                        ->where('sender_id', request()->user()->id);
                })
                ->orWhere(function ($query) use ($id) {
                    $query->where('sender_id', $id)
                        ->where('receiver_id', request()->user()->id);
                })
                ->oldest()
                ->get()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
