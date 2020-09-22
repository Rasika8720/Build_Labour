<?php

namespace App\Models\Companies;

use App\Models\BaseModel;

class CompanySpecialized extends BaseModel
{
    protected $table = 'company_specialization';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'company_id'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function store($data) {

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
