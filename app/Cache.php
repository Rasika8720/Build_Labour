<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    protected $table = 'cache';

    protected $fillable = ['type', 'json_content'];

}
