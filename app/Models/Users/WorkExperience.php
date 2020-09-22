<?php

namespace App\Models\Users;

use App\Helpers\Utils;
use App\Models\BaseModel;
use App\Models\Companies\Company;
use App\Models\Companies\Job;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkExperience extends BaseModel
{

    private $userId = null;

    protected $table = 'work_experience';
    protected $primaryKey = 'id';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'job_role', 'company_name', 'location', 'project_size', 'user_id', 'company_id',
        'start_month', 'start_year', 'end_month', 'end_year', 'isCurrent', 'duration_number', 'duration_type'
    ];

    protected $appends = ['responsibilities', 'company_photo', 'job_responsibilities'];

    protected $hidden = ['ResponsibilitiesDetail'];

    public $isOnboarding = false;

    /**
     * @return array
     */
    private function rules()
    {

        if ($this->isOnboarding) {

            return [];
        }

        return [
            'job_role'      => 'required',
            'company_name'  => 'required',
            //'project_size'  => 'nullable|regex:/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/', /* monetary validation */
            'project_size'  => 'nullable', /* monetary validation */

            'location'      => 'nullable|string',
            'start_month'   => 'nullable|integer',
            'start_year'    => 'required|integer',
            'end_month'     => 'nullable|integer',
            'end_year'      => 'nullable|integer',
            'user_id'       => 'required|integer',
            'company_id'    => 'nullable|integer'
        ];
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate( $data ){

        $validator = \Validator::make($data, $this->rules());

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        if (isset($data['end_year']) && isset($data['end_month'])) {

            $start = date("Y-m",strtotime($data['start_year'] . "-" . $data['start_month']));
            $end = date("Y-m",strtotime($data['end_year'] . "-" . $data['end_month']));

            if ($start > $end) { // invalid employment

                $validator->errors()->add( 'end_year', 'End of employment should not be earlier from the start' );

                $this->errors = $validator->errors()->all();
                $this->errorsDetail = $validator->errors()->toArray();

                return false;
            }
        }


        return true;
    }

    public function setUserId($userId) {

        $this->userId = $userId;
    }

    public function User() {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Company() {

        return $this->belongsTo( Company::class, 'company_id', 'id');
    }

    public function ResponsibilitiesDetail() {

        return $this->hasMany(WorkExperienceResponsibility::class, 'work_experience_id', 'id');
    }

    public function getCompanyPhotoAttribute() {

        if ($this->Company && $this->Company->photo_url) {

            return $this->Company->photo_url;
        }

        return null;
    }

    public function getJobResponsibilitiesAttribute() {

        if ($this->Job) {

            return $this->Job->responsibilities;
        }

        return null;
    }

    public function getResponsibilitiesAttribute() {

        $responsibilities = [];

        foreach ($this->responsibilitiesDetail as $r) {

            $responsibilities[] = $r->responsibility;
        }

        return $responsibilities;
    }

    /*
    public function setProjectSizeAttribute($value) {

        if ( ! empty( $value ) ) {

            $format = preg_replace("/[^0-9.]/","",$value);
            $format = number_format($format,0);
            $this->attributes['project_size'] = $format;

        } else {
            $this->attributes['project_size'] = null;
        }
    }
    */

    public function setIsCurrentAttribute($value) {

        if ( !empty($value) ) {

            $this->attributes['isCurrent'] = $value;

        } else {

            $this->attributes['isCurrent'] = 0;
        }
    }

    public function store(Request $r) {

        $data = $r->all();

        $pk = $this->primaryKey;

        if ($r->$pk) {

            $this->exists = true;
            $data['updated_at'] = Carbon::now();

        } else {

            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
        }

        $data['user_id'] = $this->userId;

        if (isset($data['isCurrent']) && $data['isCurrent']) {

            $data['end_month'] = null;
            $data['end_year'] = null;
        }

        if (!$this->validate($data)) {

            return false;
        }

        if ($r->most_recent_role) {

            $data['job_role'] = $r->most_recent_role;
            $data['isCurrent'] = true;
        }

        $this->fill( $data );

        if ($r->company_id && $company = Company::find($r->company_id)) {

            $this->company_name = null;

        } else {

            $this->company_id = null;
        }

        try {

            $this->save();

        } catch (\Exception $e){

            $this->errors[] = $e->getMessage();

            return false;
        }

        return $this;
    }
}
