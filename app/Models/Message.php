<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends BaseModel
{
    protected $fillable = ['body'];
    protected $appends = ['selfMessage'];

    public function user()
    {
        return $this->belongsTo(\App\Models\Users\Users::class);
    }

    public function getSelfMessageAttribute()
    {
        return $this->user_id === auth()->user()->id;
    }
}
