<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserFolloweesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Models\User  $users
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(User $users, $id)
    {
        return response()->json($users->findOrFail($id)->followees);
    }
}
