<?php

namespace DreamTeam\AdminUser\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Enums\BaseStatusEnum;
use Laravel\Sanctum\HasApiTokens;
use DreamTeam\Media\Facades\RvMedia;

class AdminUser extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use HasApiTokens, Notifiable, Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'slug', 'password', 'position', 'display_name', 'phone', 'address', 'birthday', 'avatar', 'summary', 'infomation', 'social', 'admin_user_role_id', 'capabilities', 'status', 'is_supper_admin', 'enabel_google2fa', 'google2fa_secret'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'fullname',
        'avatar_url'
    ];

    public function adminUserRole() {
        return $this->belongsTo(AdminUserRole::class, 'admin_user_role_id', 'id')->active();
    }

    /**
     * Kiểm tra phân quyền admin của tại khoản
     * @param string $role: quyền cần kiểm tra 
     */
    public function hasRole($role) {
        if ($this->id == 1 || $this->is_supper_admin == 1) {
            return true;
        } else {
            $array_role = $this->getRole();
            // Quyền cho toàn bộ tài khoản đăng nhập truy cập
            $role_default = [ 'home', 'media_view' ];
            $array_role = array_merge($role_default, $array_role);
            // Quyền riêng cho tài khoản
            $prefix = substr($role, 0, strpos($role, '_index'));
            $privateRole = $prefix . '_private';
            if(strpos($role, '_index') !== false && in_array($privateRole, $array_role)) {
                return true;
            }
            // Kiểm tra quyền
            if(in_array($role, $array_role)) {
                return true;
            } else{
                return false;
            }
        }
    }

    /**
     * Lấy ra mảng các quyền
     */
    public function getRole() {
        $roleGroup = $this->adminUserRole;
        $role = [];
        if ($roleGroup && $roleGroup->status == BaseStatusEnum::ACTIVE) {
            $role = (array) json_decode($roleGroup->permisions ?? '');
        }
        if (!empty($this->capabilities)) {
            return array_merge($role, json_decode($this->capabilities));
        } else {
            return $role;
        }
    }

    /**
     * Lấy ra tên hiển thị của tài khoản admin 
     */
    public function getName() {
        return $this->display_name ?? $this->name;
    }

    /**
     * Lấy ra ảnh đại diện của tài khoản admin 
     */
    public function getAvatar($size=null) {
        return $this->avatar;
    }

    public function getAvatarUrlAttribute($size=null) {
        return $this->avatar ? RvMedia::url($this->avatar) : RvMedia::getDefaultImage();
    }

    public function getFullnameAttribute($size=null) {
        return $this->getName();
    }

    /**
     * Ecrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function setGoogle2faSecretAttribute($value)
    {
        $this->attributes['google2fa_secret'] = encrypt($value);
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function getGoogle2faSecretAttribute($value)
    {
        if ($value == '')
        {
            return false;
        }
        return decrypt($value);
    } 

    public function getUrl($device='app'){
        return null;
    }

    public function hasAnyPermission($permisions): bool
    {
        if($this->id == 1) {
            return true;
        }
        foreach ($permisions as $key => $permision) {
            if($this->hasRole($permision)) {
                return true;
            }
        }
        return false;
    }
}
