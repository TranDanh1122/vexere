<?php

namespace DreamTeam\Ecommerce\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;
use DreamTeam\Ecommerce\Models\EmFilter;

class FilterRequest extends Request
{

    public function rules()
    {
        if ($this->route()->getName() == 'admin.filters.update') {
            $rules['filter_name'] = ['required', 'string', 'max:191', 'unique:filters,name,' . $this->route('filter')];
        } else {
            $rules = [
                'name' => ['required', 'string', 'max:191', 'unique:filters'],
                'filter_details' => ['required', 'array'],
                'filter_details.name.*' => ['required', 'string', 'max:191', 'distinct'], // Adding 'distinct' to ensure unique values in the array
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.title')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Ecommerce::admin.title')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Ecommerce::admin.title'), 'max' => 191]),
            'name.unique' => __('Core::admin.general.unique', ['name' => __('Ecommerce::admin.title')]),
            'filter_name.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.title')]),
            'filter_name.string' => __('Core::admin.general.string', ['name' => __('Ecommerce::admin.title')]),
            'filter_name.max' => __('Core::admin.general.max', ['name' => __('Ecommerce::admin.title'), 'max' => 191]),
            'filter_name.unique' => __('Core::admin.general.unique', ['name' => __('Ecommerce::admin.title')]),
            'filter_details.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.filter_detail')]), 
            'filter_details.name.*.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.filter_detail_name')]),
            'filter_details.name.*.string' => __('Core::admin.general.string', ['name' => __('Ecommerce::admin.filter_detail_name')]),
            'filter_details.name.*.max' => __('Core::admin.general.max', ['name' => __('Ecommerce::admin.filter_detail_name'), 'max' => 191]),
            'filter_details.name.*.distinct' => __('Core::admin.general.unique', ['name' => __('Ecommerce::admin.filter_detail_name')]), 
        ];
    }

}
