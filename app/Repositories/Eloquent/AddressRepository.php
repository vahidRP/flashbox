<?php

namespace App\Repositories\Eloquent;

use App\Models\Address;
use App\Repositories\Interfaces\AddressRepositoryInterface;
use App\Support\Repository\EloquentRepository;

/**
 * Class AddressRepository.
 */
class AddressRepository extends EloquentRepository implements AddressRepositoryInterface
{
    /**
     * AddressRepository constructor.
     *
     * @param Address $model
     * @throws \ReflectionException
     */
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }
}
