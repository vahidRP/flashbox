<?php

namespace App\Http\Resources;

class PermissionResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['roles'] = RoleResource::collection($this->whenLoaded('roles'));

        return $data;
    }
}
