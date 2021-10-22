<?php

namespace App\Http\Resources;

class AddressResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
