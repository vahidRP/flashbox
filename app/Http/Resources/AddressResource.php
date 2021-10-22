<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\MissingValue;

class AddressResource extends Resource
{
    /**
     * {@inheritdoc}
     **/
    public function toArray($request)
    {
        $data = parent::toArray($request);

        if (!($this->whenLoaded('userable') instanceof MissingValue) && $this->userable) {
            $baseName = class_basename($this->userable);
            $resource = "\\App\\Http\\Resources\\{$baseName}Resource";
            $data['userable'] = new $resource($this->userable);
        }

        return $data;
    }
}
