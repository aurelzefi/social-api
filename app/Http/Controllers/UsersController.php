<?php

namespace App\Http\Controllers;

use App\Models\User;

class UsersController extends Controller
{
    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $users
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(User $users, $id)
    {
        return response()->json(
            $users->withCount('posts', 'followers', 'followees')->findOrFail($id)
        );
    }
}
