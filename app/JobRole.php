<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobRole extends Model
{
    public $fillable = ['id', 'job_role_name'];

    public $timestamps = false;
}
