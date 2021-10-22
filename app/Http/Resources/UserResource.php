<?php

namespace App\Http\Resources;

class UserResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['address'] = new AddressResource($this->whenLoaded('address'));
        $data['roles'] = RoleResource::collection($this->whenLoaded('roles'));
        $data['stores'] = StoreResource::collection($this->whenLoaded('stores'));

        return $data;
    }
}
