<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyPriceResource extends JsonResource
{
    private $map = [
        'P' => 'Price',
        'S' => 'Sold Price',
        ];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'price' => $this->price,
            'price_type' => $this->map[$this->price_type],
            'created_at' => $this->created_at,
            ];
    }
}
