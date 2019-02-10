<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserFollowersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Models\User  $users
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $users, $id)
    {
        return response()->json($users->findOrFail($id)->followers);
    }
}
