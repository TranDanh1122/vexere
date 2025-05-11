<?php

namespace DreamTeam\AdminUser\Http\Requests;

use DreamTeam\Form\Http\Requests\Request;

class ForgotPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|string',
        ];
    }
}
