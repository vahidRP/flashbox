<?php

namespace App\Http\Resources;

class ProductResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['store'] = new StoreResource($this->whenLoaded('store'));

        return $data;
    }
}
