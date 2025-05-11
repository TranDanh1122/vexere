<?php

namespace DreamTeam\AdminUser\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DreamTeam\AdminUser\Http\Requests\LoginRequest;
use DreamTeam\Base\Http\Responses\BaseHttpResponse;

class AuthenticationController
{
    /**
     * Login
     *
     * @bodyParam login string required The email/phone of the user.
     * @bodyParam password string required The password of user to create.
     *
     * @response {
     * "error": false,
     * "data": {
     *    "token": "1|aF5s7p3xxx1lVL8hkSrPN72m4wPVpTvTs..."
     * },
     * "message": null
     * }
     *
     * @group Authentication
     */
    public function login(LoginRequest $request, BaseHttpResponse $response)
    {
        $input = $request->input('email');
        $field = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        if (Auth::guard('admin')->attempt([
            $field => $input,
            'password' => $request->input('password'),
        ])) {
            $token = $request->user('admin')->createToken($request->input('token_name', 'Personal Access Token'));

            return $response
                ->setData(['token' => $token->plainTextToken]);
        }

        return $response
            ->setError()
            ->setCode(422)
            ->setMessage(__('Email or password is not correct!'));
    }

    /**
     * Logout
     *
     * @group Authentication
     * @authenticated
     */
    public function logout(Request $request, BaseHttpResponse $response)
    {
        if (! $request->user()) {
            abort(401);
        }

        $request->user()->tokens()->delete();

        return $response
            ->setMessage(__('You have been successfully logged out!'));
    }
}
