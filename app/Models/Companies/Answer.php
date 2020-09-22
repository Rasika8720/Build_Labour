<?php

namespace App\Models\Companies;

use App\Models\BaseModel;

class Answer extends BaseModel
{
    protected $table = 'company_main_function_answers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'main_function_id',
        'answer'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

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
