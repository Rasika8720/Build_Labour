<?php

namespace App\Models\Options;

use App\Models\Companies\Answer;
use Illuminate\Database\Eloquent\Model;

class MainFunction extends Model
{
    protected $table = 'company_main_functions';

    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;

    public function items()
    {
        return $this->hasMany(SecondaryFunction::class, 'main_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'main_function_id', 'id');
    }
}
