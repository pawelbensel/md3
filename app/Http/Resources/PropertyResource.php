<?php

namespace App\Http\Resources;

use App\Helpers\StringHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'addresses' => new PropertyAddressCollection($this->addresses),
            'zips' => $this->zips->pluck('zip'),
            'basements' => $this->basements->pluck('basement'),
            'descriptions' => $this->descriptions->pluck('descriptions'),
            'garages' => $this->garages->pluck('garage'),
            'year_built' => $this->yearBuilds->pluck('year_build'),
            'picture_urls' => $this->pictureUrls->pluck('picture_url'),
            'square_feets' => $this->squareFeets->pluck('square_feet'),
            'transactions' => new PropertyStatusCollection($this->transactions),
            'total_rooms' => $this->totalRooms->pluck('total_room'),
            'total_bedrooms' => $this->totalBedRooms->pluck('total_bed_room'),
            'total_diningrooms' => $this->totalDiningRooms->pluck('total_dining_room'),
            'total_eat_in_kitchens' => $this->totalEatInKitchens->pluck('total_eat_in_kitchen'),
            'total_familyrooms' => $this->totalFamilyRooms->pluck('total_family_room'),
            'total_livingrooms' => $this->totalLivingRooms->pluck('total_living_room'),
            'on_markets' => $this->onMarkets->pluck('on_market','created_at'),
            'mls_ids' => new MlsIdCollection($this->mlsIds),
            'agent_mls_ids' => new MlsIdCollection($this->agentMlsIds),
            'agent_mls_office_ids' => new MlsIdCollection($this->mlsOfficeIds),
            'mls_private_numbers' => $this->mlsPrivateNumbers->pluck('mls_private_number'),
        ];
    }
}
