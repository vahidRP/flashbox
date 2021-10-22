<?php

namespace App\Http\Resources;

class RoleResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
