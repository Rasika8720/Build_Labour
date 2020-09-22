<?php

namespace App\Models\Companies;

use App\Models\BaseModel;

class JobRole extends BaseModel
{
    protected $table = 'job_roles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'job_role_name',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * @return array
     */
    private function rules()
    {
        return [
            'job_role_name' => 'required'
        ];
    }

    private function validate( $data ){

        $validator = \Validator::make($data, $this->rules());

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        return true;
    }

    public function store($data) {


        if (!$this->validate($data)) {

            return false;
        }

        $this->fill( $data );

        $pk = $this->primaryKey;

        if (isset($data[$pk])) {

            $this->exists = true;
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
