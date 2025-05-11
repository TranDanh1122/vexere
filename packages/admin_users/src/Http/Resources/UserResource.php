<?php

namespace DreamTeam\AdminUser\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar_url,
            'address' => $this->address,
            'birthday' => $this->birthday,
            'summary' => $this->summary,
            'infomation' => $this->infomation,
            'is_super_admin' => $this->is_supper_admin,
        ];
    }
}
