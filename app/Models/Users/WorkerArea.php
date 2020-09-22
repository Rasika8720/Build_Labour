<?php

namespace App\Models\Users;

use App\Models\BaseModel;

class WorkerArea extends BaseModel
{

    protected $table = 'worker_areas';
    protected $primaryKey = 'id';

    protected $fillable = [ 'business_type_id', 'worker_id' ];

    const UPDATED_AT = null;
    const CREATED_AT = null;

}
