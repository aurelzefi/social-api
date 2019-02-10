<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Notifications\UserFollowed;

class FollowingsController extends Controller
{
    /**
     * Follow the given user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow($id)
    {
        request()->merge(compact('id'))->validate([
            'id' => [
                'required',
                'exists:users',
                'not_in:'.request()->user()->id,
                Rule::unique('followings', 'followee_id')
                    ->where('follower_id', request()->user()->id),
            ],
        ]);

        request()->user()->followees()->attach($id);

        $followee = request()->user()->followees()->find($id);

        $followee->notify(
            new UserFollowed(request()->user())
        );

        return response()->json($followee);
    }

    /**
     * Unfollow the given user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow($id)
    {
        request()->merge(compact('id'))->validate([
            'id' => 'required|exists:users',
        ]);

        request()->user()->followees()->detach($id);

        return response()->json('', 204);
    }
}
