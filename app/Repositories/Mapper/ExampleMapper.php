<?php

namespace App\Repositories\Mapper;

use App\Support\Repository\Contracts\MapperInterface;

/**
 * Class ExampleMapper
 *
 * @package App\Repositories\Mapper
 */
class ExampleMapper implements MapperInterface
{
    /**
     * LdapMapper constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param \LdapRecord\Models\Collection $result
     * @return mixed
     */
    public function apply($result)
    {
        return [
            'data' => $result,
        ];
    }
}
