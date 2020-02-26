<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MlsIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'mls_name' => $this->mls_name,
            'mls_id' => $this->mls_id,
        ];
    }
}
