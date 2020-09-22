<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Models\Users\WorkExperience;
use Carbon\Carbon;

class UsersResource extends JsonResource
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
        $dt = Carbon::parse($this->created_at);
        $created_at = $dt->toFormattedDateString();
        return [
            'full_name' => $profile_photo_url . ' ' . $this->first_name . ' ' . $this->last_name,
            'id'        => $this->id,
            'email'     => $this->email,
            'role'      => $this->role,
            'address'   => $this->address,
            'sector'    => $this->sector,
            'tier'      => $this->tier,
            'willing_to_relocate' => $this->willing_to_relocate,
            'created_at' => $created_at
        ];
    }
}
