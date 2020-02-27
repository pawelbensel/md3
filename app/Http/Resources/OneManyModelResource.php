<?php

namespace App\Http\Resources;

use App\Models\Agent;
use App\Models\Office;
use App\Models\Prop;
use Illuminate\Http\Resources\Json\JsonResource;

class OneManyModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->resource instanceof Office) {
            return new OfficeResource($this->resource);
        }
        if($this->resource instanceof Agent) {
            return new AgentResource($this->resource);
        }
        if($this->resource instanceof Prop) {
            return new PropertyResource($this->resource);
        }

        throw new \Exception('Unsupported type to serialize');
    }
}
