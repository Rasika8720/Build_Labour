<?php

namespace App\Models\Companies;

use App\Models\BaseModel;

class JobStat extends BaseModel
{
    protected $table = 'job_post_stats';

    protected $primaryKey = 'id';

    protected $fillable = [
        'job_id',
        'scored_to',
        'performed_by',
        'category',
        'created_at'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

}
