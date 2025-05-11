<?php

namespace DreamTeam\Translate\Http\Requests;

use DreamTeam\Base\Supports\Language;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;

class LanguageRequest extends Request
{
    public function rules(): array
    {
        return [
            'lang_name' => 'required|string|max:30|min:2',
            'lang_code' => [
                'required',
                'string',
                Rule::in(Language::getLanguageCodes()),
            ],
            'lang_locale' => [
                'required',
                'string',
                Rule::in(Language::getLocaleKeys()),
            ],
            'lang_flag' => 'required|string',
            'lang_is_rtl' => 'required|boolean',
            'lang_order' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'lang_name.required' => __('Core::admin.general.require', ['name' => __('Translate::language.language_name')]),
            'lang_code.required' => __('Core::admin.general.require', ['name' => __('Translate::language.language_code')]),
            'lang_locale.unique' => __('Core::admin.general.unique', ['name' => __('Translate::language.language_locale')]),
            'lang_flag.unique' => __('Core::admin.general.unique', ['name' => __('Translate::language.flag')])
        ];
    }
}
