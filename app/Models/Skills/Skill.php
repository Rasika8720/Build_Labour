<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills';

    protected $fillable = [ 'name' ];

    const UPDATED_AT = null;
    const CREATED_AT = null;
    
}