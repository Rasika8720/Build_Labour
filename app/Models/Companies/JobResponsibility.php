<?php

namespace App\Models\Companies;

use App\Models\BaseModel;

class JobResponsibility extends BaseModel
{
    protected $table = 'job_responsibilities';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'items_json',
        'job_id'
    ];

    protected $appends = [
        'items'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function setItemsJsonAttribute($data) {

        if (!empty($data) && is_array($data)) {

            $this->attributes['items_json'] = json_encode($data);

        } else {

            $this->attributes['items_json'] = NULL;
        }

    }

    public function getItemsAttribute() {

        return json_decode($this->items_json);
    }

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
