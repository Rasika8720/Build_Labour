<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use App\User;

class Admin extends BaseModel
{

    protected $table = 'administrators';
    protected $primaryKey = 'id';

    public function User() {

        return $this->belongsTo(User::class, 'id', 'user_id');
    }

}
