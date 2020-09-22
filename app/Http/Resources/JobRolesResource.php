<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobRolesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'job_role_name' => $this->job_role_name
        ];
    }
}
