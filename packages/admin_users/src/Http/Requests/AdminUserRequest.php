<?php

namespace DreamTeam\AdminUser\Http\Requests;

use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;
use DreamTeam\Form\Rules\UniqueSlug;
use DreamTeam\AdminUser\Models\AdminUser;

class AdminUserRequest extends Request
{

    public function rules()
    {
        if ($this->route()->getName() == 'admin.admin_users.store') {
            $rules = [
                'name' => 'required|string|max:191',
                'email' => 'required|email',
                'slug' => ['nullable', 'string', 'max:191', 'unique:slugs,slug'],
                'phone' => 'required|max:10',
            ];
            $rules['password'] = 'required';
            $rules['password_confirm'] = 'required|same:password_confirm';
        }
        if ($this->route()->getName() == 'admin.admin_users.update' || $this->route()->getName() == 'admin.admin_users.change_info_post') {
            $rules = [
                'slug' => ['nullable', 'string', 'max:191'],
            ];
            if ($this->route()->getName() == 'admin.admin_users.change_info_post') {
                $rules['slug'][] = new UniqueSlug($this->route('id'), (new AdminUser())->getTable());
            } else {
                $rules['slug'][] = new UniqueSlug($this->route('admin_user'), (new AdminUser())->getTable());
            }
            if($this->input('change_password')) {
                $rules['password'] = 'required';
                $rules['password_confirm'] = 'required|same:password_confirm';
            }
        }
        if ($this->route()->getName() == 'admin.admin_users.change_password_post') {
            $rules['password'] = 'required';
            $rules['password_confirm'] = 'required|same:password_confirm';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.title')]),
            'name.phone' => __('Core::admin.general.require', ['name' => __('AdminUser::admin.phone')]),
            'name.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.title')]),
            'name.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.title'), 'max' => 191]),
            'phone.max' => __('Core::admin.general.max', ['name' => __('AdminUser::admin.phone'), 'max' => 10]),
            'email.required' => __('Core::admin.general.require', ['name' => __('Email')]),
            'email.email' => __('Core::admin.general.wrong_of', ['name' => __('Email')]),
            'slug.required' => __('Core::admin.general.require', ['name' => __('Core::admin.general.slug')]),
            'slug.string' => __('Core::admin.general.string', ['name' => __('Core::admin.general.slug')]),
            'slug.max' => __('Core::admin.general.max', ['name' => __('Core::admin.general.slug'), 'max' => 191]),
            'slug.unique' => __('Core::admin.general.unique', ['name' => __('Core::admin.general.slug')]),
            'password.required' => 'Core::admin.validate.required_password',
            'password_confirm.required' => 'Core::admin.validate.required_password_comfirm',
            'password_confirm.same' => 'Core::admin.validate.required_equal',
        ];
    }
}
