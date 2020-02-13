<?php

namespace App\Http\Resources;

use App\Models\Agent;
use App\Models\Office;
use App\Models\Prop;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlySimilarResource extends JsonResource
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

        if($this->whenLoaded('similar')){

            if($this->similar instanceof Office){
                $similar = new OfficeResource($this->similar);
            }
            if($this->similar instanceof Agent) {
                $similar = new AgentResource($this->similar);
            }
            if($this->similar instanceof Prop) {
                $similar = new PropertyResource($this->similar);
            }
        }

        return $similar;
    }
}
