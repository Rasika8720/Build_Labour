<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeoplesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $profile_photo_url = ($this->profile_photo_url == null) ? '/img/defaults/user.png' : $this->profile_photo_url;

        $exp = null;
        $jobRole = '';

        if ($this->workerDetail) {
            $exp = $this->workerDetail->getLatestExperience();
        }

        if ($exp) {
            $jobRole = $exp->job_role;
        }

        return [
            'id' => $this->id,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'profile_photo_url' => $this->profile_photo_url,
            'job_role' => $jobRole,
            'address' => $this->address
        ];
    }
}
