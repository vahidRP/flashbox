<?php

namespace App\Http\Resources;

class StoreResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
