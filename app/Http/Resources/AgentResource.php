<?php

namespace App\Http\Resources;

use App\Helpers\StringHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(StringHelpers::contains($request->getPathInfo(), 'history')){
            $this->loadAllHasMany(true);
        }
        return [
            'id' => $this->id,
            'type' => class_basename($this->resource),
            'first_names' => $this->firstNames->pluck('first_name'),
            'last_names' => $this->lastNames->pluck('last_name'),
            'license_numbers' => $this->licenseNumbers->pluck('license_number'),
            'phones' => $this->phones->pluck('phone'),
            'types' => $this->types->pluck('types'),
            'titles' => $this->titles->pluck('titles'),
            'mls_ids' => new MlsIdCollection($this->mlsIds),
        ];
    }
}
