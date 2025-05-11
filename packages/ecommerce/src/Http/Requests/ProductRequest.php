<?php

namespace DreamTeam\Ecommerce\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;
use DreamTeam\Form\Rules\UniqueSlug;
use DreamTeam\Ecommerce\Models\Product;

class ProductRequest extends Request
{

    public function rules()
    {
        $rules = [
            'name'        => 'required|string|max:191',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.title')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.title')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.title'), 'max' => 191]),
            'category_id.required' => __('Core::admin.general.require', ['name' => __('Ecommerce::admin.category_id')]),
            'slug.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.slug')]),
            'slug.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.slug')]),
            'slug.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.slug'), 'max' => 191]),
            'slug.unique' => __('Core::admin.general.unique', ['name' => __('Core::admin.general.slug')]),
        ];
    }
}
