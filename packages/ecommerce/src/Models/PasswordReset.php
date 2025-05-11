<?php

namespace DreamTeam\Ecommerce\Models;
use DreamTeam\Base\Models\BaseModel;

class PasswordReset extends BaseModel
{
    protected $table = 'password_resets';

    protected $guarded  = ['id'];

    public $timestamps = false;

}
