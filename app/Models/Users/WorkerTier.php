<?php

namespace App\Models\Users;

use App\Models\BaseModel;

class WorkerTier extends BaseModel
{

    protected $table = 'worker_tiers';
    protected $primaryKey = 'id';

    protected $fillable = [ 'tier_id', 'worker_id' ];

    const UPDATED_AT = null;
    const CREATED_AT = null;

}
