<?php

namespace App\Http\Resources;

class ProductResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
