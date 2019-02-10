<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class AvatarController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Contracts\Filesystem\Factory  $files
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(FilesystemFactory $files)
    {
        request()->validate([
            'avatar' => 'required|mimes:jpeg,bmp,png',
        ]);

        $files->disk('public')->delete(request()->user()->avatar);

        request()->user()->fill([
            'avatar' => request()->file('avatar')->store('/', 'public'),
        ])->save();

        return response()->json(request()->user());
    }
}
