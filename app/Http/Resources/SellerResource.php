<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
        ];

        if ($request->isMethod('get')) {
            $data['total_comission'] = $this->getTotalCommission();
        }

        return $data;
    }
}
