<?php

namespace DreamTeam\AdminUser\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DreamTeam\AdminUser\Http\Requests\ForgotPasswordRequest;
use Password;

class ForgotPasswordController extends Controller
{

    /**
     * Forgot password
     *
     * Send a reset link to the given user.
     *
     * @bodyParam email string required The email of the user.
     *
     * @group Authentication
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    public function broker()
    {
        return Password::broker(ApiHelper::passwordBroker());
    }
}
