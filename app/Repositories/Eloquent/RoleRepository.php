<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Support\Repository\EloquentRepository;

/**
 * Class RoleRepository.
 */
class RoleRepository extends EloquentRepository implements RoleRepositoryInterface
{
    /**
     * RoleRepository constructor.
     *
     * @param Role $model
     * @throws \ReflectionException
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
