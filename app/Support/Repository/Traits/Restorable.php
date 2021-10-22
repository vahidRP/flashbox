<?php

namespace App\Support\Repository\Traits;

trait Restorable
{
    /**
     * @param        $value
     * @param string $field
     */
    public function restore($value, $field = 'id')
    {
        $this->model->where($field, $value)->restore();
    }
}
