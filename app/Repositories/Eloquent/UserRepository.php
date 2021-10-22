<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Support\Repository\EloquentRepository;

/**
 * Class UserRepository.
 */
class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     * @throws \ReflectionException
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
