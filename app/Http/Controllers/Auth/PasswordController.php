<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        request()->validate([
            'password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check(request('password'), request()->user()->password)) {
            throw ValidationException::withMessages([
                'password' => 'The current password field does not match.',
            ]);
        }

        request()->user()->fill([
            'password' => Hash::make(request('new_password')),
        ])->save();

        return response()->json(request()->user());
    }
}
