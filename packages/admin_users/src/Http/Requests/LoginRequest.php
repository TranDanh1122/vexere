<?php

namespace DreamTeam\AdminUser\Http\Requests;

use DreamTeam\Form\Http\Requests\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
