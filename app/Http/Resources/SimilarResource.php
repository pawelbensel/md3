<?php

namespace App\Http\Resources;

use App\Models\Office;
use Illuminate\Http\Resources\Json\JsonResource;

class SimilarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $similar = [];
        $object = [];

        if($this->whenLoaded('similar')){

          if($this->similar instanceof Office){
            $similar = new OfficeResource($this->similar);
          }
        }
        if($this->whenLoaded('object')){

            if($this->object instanceof Office){
                $object = new OfficeResource($this->object);
            }
        }



        return [
                'id' => $this->id,
                'object' => $object,
                'similar' => $similar,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
        ];
    }
}
