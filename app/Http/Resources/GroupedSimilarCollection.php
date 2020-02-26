<?php

namespace App\Http\Resources;

use App\Models\Agent;
use App\Models\Office;
use App\Models\Prop;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupedSimilarCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $object = $this->collection->first()->object;
        switch(true){
            case $object instanceof Office:
                $object = new OfficeResource($object);
                break;
            case $object instanceof Agent:
                $object = new AgentResource($object);
                break;
            case $object instanceof Prop:
                $object = new PropertyResource($object);
                    break;
        }

        $similars = $this->collection->map(function($similar){
            return [
                    'id' => $similar->id,
                    'matching_rate' => $similar->matching_rate,
                    'matched_by' => $similar->matched_by,
                    'merged_at' => $similar->deleted_at,
                    'similar' => new OnlySimilarResource($similar),
                ];
        });

        return ['object' => $object,
                'similars' => $similars];
    }
}
