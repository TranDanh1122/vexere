<?php

namespace DreamTeam\Ecommerce\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;
use DreamTeam\Form\Rules\UniqueSlug;
use DreamTeam\Ecommerce\Models\Brand;

class BrandRequest extends Request
{

    public function rules()
    {
        $rules = [
            'name'        => 'required|string|max:191|unique:brands,name,' . $this->route('brand'),
            'ower_name'        => ['required', 'string', 'max:191'],
            'ower_phone'        => ['required', 'string', 'max:191'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.brand_name')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Ecommerce::admin.brand_name')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Ecommerce::admin.brand_name'), 'max' => 191]),
            'slug.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.slug')]),
            'slug.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.slug')]),
            'slug.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.slug'), 'max' => 191]),
            'slug.unique' => __('Core::admin.general.unique', ['name' => __('Core::admin.general.slug')]),
        ];
    }
}
