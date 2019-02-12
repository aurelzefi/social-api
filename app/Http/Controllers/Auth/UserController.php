<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;

class UserController extends Controller
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $files
     * @param  \App\Models\User  $users
     * @return void
     */
    public function __construct(Filesystem $files, User $users)
    {
        $this->files = $files;
        $this->users = $users;
    }

    /**
     * Display the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return response()->json(
            $this->users->findForApiToken(request()->bearerToken())
        );
    }

    /**
     * Update the authenticated user in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        request()->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore(request()->user()->id),
            ],
        ]);

        request()->user()->fill([
            'name' => request('name'),
            'email' => request('email'),
        ])->save();

        return response()->json(
            $this->users->findForApiToken(request()->bearerToken())
        );
    }
}
