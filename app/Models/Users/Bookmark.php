<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use App\Models\Companies\CompanyPost;
use App\Models\Companies\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Bookmark extends BaseModel
{

    protected $table = 'bookmarks';
    protected $primaryKey = 'id';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'post_id',
        'job_id',
        'user_id'
    ];

    /**
     * @return array
     */
    private function rules()
    {
        return [
           // 'post_id'  => 'required|integer',
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

        return true;
    }

    public function User() {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function CompanyPost() {

        return $this->belongsTo(CompanyPost::class, 'post_id', 'id');
    }

    public function Job() {

        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    public function store($data) {

        $pk = $this->primaryKey;

        if (isset($data[$pk])) {

            $this->exists = true;

        }

        if (!$this->validate($data)) {

            return false;
        }

        $this->fill( $data );

        try {

            $this->save();

        } catch (\Exception $e){

            $this->errors[] = $e->getMessage();

            return false;
        }

        return $this;
    }
}
