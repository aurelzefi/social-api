<?php

namespace App\Http\Controllers;

use App\Models\User;

class SuggestionsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Models\User  $users
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(User $users)
    {
        return response()->json(
            $users
                ->where('id', '<>', request()->user()->id)
                ->whereIn('id', function ($query) {
                    $query->select('followee_id')
                        ->from('followings')
                        ->whereIn('follower_id', function ($query) {
                            $query->select('followee_id')
                                ->from('followings')
                                ->where('follower_id', request()->user()->id);
                        });
                })
                ->whereNotIn('id', function ($query) {
                    $query->select('followee_id')
                        ->from('followings')
                        ->where('follower_id', request()->user()->id);
                })
                ->inRandomOrder()
                ->take(5)
                ->get()
        );
    }
}
