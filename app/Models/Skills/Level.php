<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';

    protected $fillable = [ 'name' ];

    const UPDATED_AT = null;
    const CREATED_AT = null;
}