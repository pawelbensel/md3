<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SimilarsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $groupedBy = $this->collection->groupBy('object_id');


        return $groupedBy->map(function($item){
            return new GroupedSimilarCollection($item);
        });
    }
}
