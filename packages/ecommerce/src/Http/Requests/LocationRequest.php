<?php

namespace DreamTeam\Ecommerce\Http\Requests;

use DreamTeam\Form\Http\Requests\Request;

class LocationRequest extends Request
{

    public function rules()
    {
        $rules = [
            'name'        => 'required|string|max:191|unique:locations,name,' . $this->route('location'),
            'from'        => ['required', 'string', 'in:sg,vt'],
            'address'     => 'required|string',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.location_name')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Ecommerce::admin.location_name')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Ecommerce::admin.location_name'), 'max' => 191]),
        ];
    }
}
