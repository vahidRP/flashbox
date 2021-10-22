<?php

namespace App\Http\Resources;

class ReceiptResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['products'] = ProductResource::collection($this->whenLoaded('products'));

        return $data;
    }
}
