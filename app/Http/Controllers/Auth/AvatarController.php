<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class AvatarController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Contracts\Filesystem\Factory  $files
     * @param  \App\Models\User  $users
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(FilesystemFactory $files, User $users)
    {
        request()->validate([
            'avatar' => 'required|mimes:jpeg,bmp,png',
        ]);

        $files->disk('public')->delete(request()->user()->avatar);

        request()->user()->fill([
            'avatar' => request()->file('avatar')->store('/', 'public'),
        ])->save();

        return response()->json(
            $users->findForApiToken(request()->bearerToken())
        );
    }
}
