<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * authorize all actions within a given policy
     *
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        //
    }

    /**
     * Determine whether the user can view any stores.
     *
     * @param  User   $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the store.
     *
     * @param  User   $user
     * @param  Store   $store
     * @return mixed
     */
    public function view(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can create stores.
     *
     * @param  User   $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the store.
     *
     * @param  User   $user
     * @param  Store   $store
     * @return mixed
     */
    public function update(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can delete the store.
     *
     * @param  User   $user
     * @param  Store   $store
     * @return mixed
     */
    public function delete(User $user, Store $store)
    {
        //
    }
}
