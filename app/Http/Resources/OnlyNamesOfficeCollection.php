<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OnlyNamesOfficeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function($singleOffice){
            return [ 'id' => $singleOffice->id,
                     'names' => $singleOffice->names->pluck('name')
            ];
        });
    }
}
