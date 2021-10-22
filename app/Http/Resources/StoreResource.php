<?php

namespace App\Http\Resources;

class StoreResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['user'] = new UserResource($this->whenLoaded('user'));
        $data['address'] = new AddressResource($this->whenLoaded('address'));

        return $data;
    }
}
