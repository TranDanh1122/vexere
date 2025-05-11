<?php

namespace DreamTeam\AdminUser\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AdminUserRoleRequest extends Request
{

    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:191'],
            'role' => ['required'],
        ];
        if ($this->route()->getName() == 'admin.admin_user_roles.update') {
        	$rules['name'][] = 'unique:admin_user_roles,name,'.$this->route('admin_user_role');
        } else {
        	$rules['name'][] = 'unique:admin_user_roles';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.title')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.title')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.title'), 'max' => 191]),
            'name.unique' => __('Core::admin.general.unique', ['name' => __('Core::admin.general.name')]),
            'role.required' => __('Core::admin.general.require', ['name' => __('AdminUser::admin.roles.name')]),
        ];
    }
}
