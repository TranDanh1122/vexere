<?php

namespace DreamTeam\SyncLink\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SyncLinkRequest extends Request
{

    public function rules()
    {
        $rules = [
            'old' => ['required'],
            'new' => ['required'],
        ];
        if ($this->route()->getName() == 'admin.sync_links.update') {
            $rules['old'][] = 'unique:sync_links,old,'.$this->route('sync_link');
        } else {
            $rules['old'][] = 'unique:sync_links';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'old.required' => __('Core::admin.general.require', ['name' => __('SyncLink::admin.source_link')]),
            'new.required' => __('Core::admin.general.require', ['name' => __('SyncLink::admin.target_link')]),
            'old.unique' => __('Core::admin.general.unique', ['name' => __('SyncLink::admin.source_link')]),
        ];
    }
}
