<?php

namespace DreamTeam\Page\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;
use DreamTeam\Form\Rules\UniqueSlug;
use DreamTeam\Page\Models\Page;

class PageRequest extends Request
{

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:191',
            'slug' => ['required', 'string', 'max:191'],
        ];

        if ($this->route()->getName() == 'admin.pages.store') {
            $rules['slug'][] = 'unique:slugs,slug';
        }
        if ($this->route()->getName() == 'admin.pages.update') {
            $rules['slug'][] = new UniqueSlug($this->route('page'), (new Page())->getTable());
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.title')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.title')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.title'), 'max' => 191]),
            'slug.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.slug')]),
            'slug.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.slug')]),
            'slug.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.slug'), 'max' => 191]),
            'slug.unique' => __('Core::admin.general.unique', ['name' => __('Core::admin.general.slug')]),
        ];
    }
}
