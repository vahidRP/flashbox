<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Support\Repository\EloquentRepository;

/**
 * Class ProductRepository.
 */
class ProductRepository extends EloquentRepository implements ProductRepositoryInterface
{
    /**
     * ProductRepository constructor.
     *
     * @param Product $model
     * @throws \ReflectionException
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
