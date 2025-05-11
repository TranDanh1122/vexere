<?php

namespace DreamTeam\AdminUser\Models;

use DreamTeam\Base\Models\BaseModel;

class AdminUserRole extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'team', 'note', 'permisions', 'status',
    ];

    public function adminUser() {
        return $this->hasMany(AdminUser::class, 'admin_user_role_id', 'id');
    }

    /**
     * Lấy ra mảng các quyền
     */
    public function getRole() {
        if (!empty($this->permisions)) {
            return json_decode($this->permisions);
        } else {
            return [];
        }
    }

}