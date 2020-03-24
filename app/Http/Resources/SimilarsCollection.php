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
        return $this->collection->groupBy(['object_type', 'object_id'])->map(function($item){
            return $item->map(function($i){
                return new GroupedSimilarCollection($i);
            });
        })->flatten();
    }
}
