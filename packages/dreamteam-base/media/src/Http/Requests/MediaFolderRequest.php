<?php

namespace DreamTeam\Media\Http\Requests;

use DreamTeam\Form\Http\Requests\Request;

class MediaFolderRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|regex:/^[\pL\s\ \_\-0-9]+$/u',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => trans('media::media.name_invalid'),
        ];
    }
}
