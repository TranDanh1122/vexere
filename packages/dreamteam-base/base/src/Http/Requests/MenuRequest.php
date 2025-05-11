<?php

namespace DreamTeam\Base\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MenuRequest extends Request
{

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'value' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.title')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.title')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.title'), 'max' => 191]),
            'value.required' => __('Core::admin.general.require', ['name' => __('Core::admin.menu.list')]),
        ];
    }
}
