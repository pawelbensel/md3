<?php

namespace App\Http\Resources;

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
            'transactions' => new PropertyStatusCollection($this->statuses),
            'total_rooms' => $this->totalRooms->pluck('total_room'),
            'total_bedrooms' => $this->totalBedRooms->pluck('total_bed_room'),
            'total_diningrooms' => $this->totalDiningRooms->pluck('total_dining_room'),
            'total_eat_in_kitchens' => $this->totalEatInKitchens->pluck('total_eat_in_kitchen'),
            'total_familyrooms' => $this->totalFamilyRooms->pluck('total_family_room'),
            'total_livingrooms' => $this->totalLivingRooms->pluck('total_living_room'),
            'on_markets' => $this->onMarkets->pluck('on_market','created_at'),
            'mls_ids' => $this->mlsIds->pluck('mls_id', 'mls_name'),
            'agent_mls_ids' => $this->agentMlsIds->pluck('agent_mls_id', 'mls_name'),
            'agent_mls_office_ids' => $this->mlsOfficeIds->pluck('mls_office_ids', 'mls_name'),
            'mls_private_numbers' => $this->mlsPrivateNumbers->pluck('mls_private_number', 'mls_name'),
        ];
    }
}
