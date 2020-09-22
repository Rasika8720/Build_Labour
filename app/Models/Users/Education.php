<?php

namespace App\Models\Users;

use App\Course;
use App\Models\BaseModel;
use App\School;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Education extends BaseModel
{

    private $userId = null;

    protected $table = 'educations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'course',
        'school',
        'school_id',
        'description',
        'start_day',
        'start_month',
        'start_year',
        'end_day',
        'end_month',
        'end_year',
        'user_id',
        'course_id',
        'education_status'
    ];

    protected $appends = [
        'course_name',
        'school_name',
        'school_logo'
    ];

    const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * @return array
     */
    private function rules()
    {
        return [
            'course_name'   => 'required',
            'school'        => 'nullable',
            'user_id'       => 'required|integer',
            'education_status' => 'nullable|in:Completed Study,Still Studying'
        ];
    }

    public function validationMessages()
    {
        return [
            'education_status.in'  => 'Education Status must be Completed Study or Still Studying',
            'date_of_birth.before' => 'You must be at least 18 years old.'
        ];
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate( $data ){

        $rules = $this->rules();

        if (isset($data['school_id'])) {

            unset($rules['school']);

            $exist = School::where('id', $data['school_id'])->exists();

            if (!$exist) {

                return false;
            }
        }

        $validator = \Validator::make($data, $rules, $this->validationMessages());

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        if ($data['education_status'] == 'Completed Study') {

            $rules = [
                'end_year'      => 'required|integer'
            ];

        } else if($data['education_status'] == 'Still Studying') {

            $rules = [
                'start_year'    => 'required|integer'
            ];

        }

        $validator = \Validator::make($data, $rules);

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        if ($data['education_status'] == 'Still Studying') {

            $start = date("Y-m-d",strtotime($data['start_year'] . "-" . $data['start_month'] . "-" . $data['start_day']));
            $end = date("Y-m-d",strtotime($data['end_year'] . "-" . $data['end_month'] . "-" . $data['end_day']));

            if ($start > $end) { // invalid education

                $validator->errors()->add( 'end_year', 'Start date should be earlier than end date' );

                $this->errors = $validator->errors()->all();
                $this->errorsDetail = $validator->errors()->toArray();

                return false;
            }
        }


        return true;
    }

    public function User() {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Course () {

        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function setUserId($userId) {

        $this->userId = $userId;
    }

    public function Academy() {

        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function getCourseNameAttribute() {

        if ($this->Course) {

            return $this->Course->course_name;
        }


        return $this->course;
    }

    public function getSchoolNameAttribute() {

        if ($this->Academy) {

            return $this->Academy->school_name;
        }


        return $this->school;
    }

    public function getSchoolLogoAttribute() {

        if ($this->Academy && $this->Academy->logo_url) {

            return $this->Academy->logo_url;
        }


        return null;
    }

    public function store(Request $r) {

        $data = $r->all();

        $data['user_id'] = $this->userId;

        if (isset($data['education_status'])) {

            // $data['education_status'] = strtolower($data['education_status']);

            if ($data['education_status'] == 'Completed Study') {

                $data['start_day'] = null;
                $data['start_month'] = null;
                $data['start_year'] = null;
            }
        }

        if( ! $this->validate( $data )) {

            return false;
        }

        $pk = $this->primaryKey;

        if ($r->$pk) {

            $this->exists = true;
            $data['updated_at'] = Carbon::now();

        } else {

            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
        }


        if (isset($data['course_name'])) {

            $data['course'] = $data['course_name'];
        }

        if (!empty($data['course_id'])) {

            $thereAreCourse = Course::where('id', $data['course_id'])->exists();

            if ($thereAreCourse) {

                $data['course'] = NULL;
            }
        }

        $this->fill( $data );

        try{

            $this->save();

        } catch (\Exception $e){

            $this->errors[] = $e->getMessage();

            return false;
        }

        return $this;
    }
}
