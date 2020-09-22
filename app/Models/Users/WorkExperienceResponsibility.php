<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Http\Request;

class WorkExperienceResponsibility extends BaseModel
{

    private $userId = null;

    protected $table = 'work_experience_responsibilities';
    protected $primaryKey = 'id';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = ['work_experience_id', 'responsibility'];


    /**
     * @return array
     */
    private function rules()
    {
        return [
            'responsibility'  => 'required'
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


    public function store(Request $r) {

        $data = $r->all();

        $pk = $this->primaryKey;

        if ($r->work_experience_responsibility_id) {

            $this->exists = true;

        }

        if ($r->id) {

            $data['work_experience_id'] = $r->id;
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