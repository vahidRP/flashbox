<?php

namespace App\Http\Resources;

class RoleResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['permissions'] = PermissionResource::collection($this->whenLoaded('permissions'));

        return $data;
    }
}
