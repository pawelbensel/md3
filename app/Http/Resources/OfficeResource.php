<?php

namespace App\Http\Resources;

use App\Helpers\StringHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
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
            'names' => $this->names->pluck('name'),
            'phones' => $this->phones->pluck('phone'),
            'addresses' => new OfficeAddressesCollection($this->addresses),
            'states' => $this->states->pluck('state'),
            'company_names' => $this->companyNames->pluck('company_names'),
            'emails' => $this->emails->pluck('email'),
            'webistes' => $this->websites->pluck('website'),
            'mls_ids' => new MlsIdCollection($this->mlsIds),
            'msa_ids' => $this->msaIds,
        ];
    }
}
