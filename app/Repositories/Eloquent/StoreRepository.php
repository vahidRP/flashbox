<?php

namespace App\Repositories\Eloquent;

use App\Models\Store;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use App\Support\Repository\EloquentRepository;

/**
 * Class StoreRepository.
 */
class StoreRepository extends EloquentRepository implements StoreRepositoryInterface
{
    /**
     * StoreRepository constructor.
     *
     * @param Store $model
     * @throws \ReflectionException
     */
    public function __construct(Store $model)
    {
        parent::__construct($model);
    }
}
