<?php

namespace App\Support\Repository\Contracts;

interface MapperInterface
{
    /**
     * The mapper to be applied must go inside this method.
     *
     * @param mixed $result result
     *
     * @return mixed $result mapped instance of the query builder with the mapper applied
     */
    public function apply($result);
}
