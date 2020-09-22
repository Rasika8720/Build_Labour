<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class JobsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $dt = Carbon::parse($this->created_at);
        $created_at = $dt->toFormattedDateString();
        $company = $this->company ? $this->company->name : '';
        return [
//            'created_at'    => $created_at,
            'id'            => $this->id,
            'company'       => $company,
            'title'         => $this->job_role_name,
            'location'      => $this->location,
            // 'description'   => $this->description,
            // 'about'         => $this->about,
            'exp_level'     => $this->exp_level,
            'contract_type' => $this->contract_type,
            'salary'        => $this->salary,
            'reports_to'    => $this->reports_to_str,


         //   'job_role_id'   => $this->job_role_id,
           // 'is_template'   => $this->is_template
        ];
    }
}
