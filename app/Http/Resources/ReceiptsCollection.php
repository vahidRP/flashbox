<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReceiptsCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return array
     **/
    public function toArray($request)
    {
        return [
            'data' => ReceiptResource::collection($this->collection)
        ];
    }
}
