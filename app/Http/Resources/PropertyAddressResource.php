<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAddressResource extends JsonResource
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
            'street_unit' => $this->street_unit,
            'street_sufix' => $this->street_sufix,
            'street_post_direction' => $this->street_post_direction,
            'street_number' => $this->street_number,
            'street_name' => $this->street_name,
            'street_direction' => $this->street_direciton,
            'county' => $this->county,
            'city' => $this->city,
        ];
    }
}
