<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CompaniesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $dt = Carbon::parse($this->created_at);
        $created_at = $dt->toFormattedDateString();

        return [
            'id'    => $this->id,
            'name'  => $this->name,
            // 'no_of_workers' => $this->no_of_workers,
            'created_at' => $created_at
        ];
    }
}
