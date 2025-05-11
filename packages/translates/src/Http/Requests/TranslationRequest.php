<?php

namespace DreamTeam\Translate\Http\Requests;

use DreamTeam\Form\Http\Requests\Request;

class TranslationRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:250',
        ];
    }
}
