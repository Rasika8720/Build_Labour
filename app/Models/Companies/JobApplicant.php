<?php

namespace App\Models\Companies;

use App\Models\BaseModel;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JobApplicant extends BaseModel
{
    protected $table = 'job_post_applicants';

    protected $primaryKey = 'id';

    protected $fillable = [
        'job_id',
        'user_id',
        'applied_at',
        'selected'
    ];

    protected $appends = [
        'applied_proper'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function User() {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Job() {

        return $this->belongsTo( Job::class, 'job_id', 'id');
    }

    public function getAppliedProperAttribute() {

        return Carbon::parse($this->applied_at)->diffForHumans();
    }

    public function getAppliedEmailsByJobId($jid) {
        $worker_emails = DB::table($this->table)
            ->select(['users.email'])
            ->leftJoin('users', 'job_post_applicants.user_id', '=', 'users.id')
            ->where('job_post_applicants.job_id', $jid)
            ->get();

        return $worker_emails;
    }
}
