<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Support\Repository\EloquentRepository;

/**
 * Class PermissionRepository.
 */
class PermissionRepository extends EloquentRepository implements PermissionRepositoryInterface
{
    /**
     * PermissionRepository constructor.
     *
     * @param Permission $model
     * @throws \ReflectionException
     */
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}
