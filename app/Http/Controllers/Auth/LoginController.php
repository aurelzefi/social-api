<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Models\User  $users
     * @return void
     */
    public function __construct(User $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->once($this->credentials($request));
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $users
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $this->guard()->user()->fill(['api_token' => $token = str_random()])->save();

        return response()->json(
            $this->users->findForApiToken($token)->makeVisible('api_token')
        );
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard()->user()->fill(['api_token' => null])->save();

        return response()->json('', 204);
    }

    /**
     * {@inheritdoc}
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
