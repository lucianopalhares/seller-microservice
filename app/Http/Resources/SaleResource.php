<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'id' => $this->getId(),
            'seller_name' => $this->getSeller()->getName(),
            'seller_email' => $this->getSeller()->getEmail(),
            'commission_value' => $this->getCommission(),
            'sale_value' => $this->getValue(),
            'sale_date' => \Carbon\Carbon::parse($this->getSaleDate())->format('d/m/Y')
        ];
    }
}
