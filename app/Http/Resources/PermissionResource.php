<?php

namespace App\Http\Resources;

class PermissionResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
