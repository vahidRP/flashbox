<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    protected string $model;

    /**
     * authorize all actions within a given policy
     *
     * @param $user
     * @param $ability
     * @return bool|void
     */
    public function before($user, $ability)
    {
        if($user->isSuperAdmin()){
            return true;
        }
    }

    /**
     * Determine whether the user can view any addresses.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(($this->model)::getTableName() . '.read');
    }

    /**
     * Determine whether the user can view the address.
     *
     * @param User    $user
     * @param $model
     * @return bool
     */
    public function view(User $user, $model): bool
    {
        return $user->hasPermission("{$model->getTable()}.read");
    }

    /**
     * Determine whether the user can create addresses.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission(($this->model)::getTableName() . '.create');
    }

    /**
     * Determine whether the user can update the address.
     *
     * @param User    $user
     * @param $model
     * @return bool
     */
    public function update(User $user, $model): bool
    {
        return $user->hasPermission("{$model->getTable()}.update");
    }

    /**
     * Determine whether the user can delete the address.
     *
     * @param User    $user
     * @param $model
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return $user->hasPermission("{$model->getTable()}.delete");
    }
}
