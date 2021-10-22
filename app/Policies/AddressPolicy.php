<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
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
     * Determine whether the user can view any addresses.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the address.
     *
     * @param  User  $user
     * @param  Address  $address
     * @return mixed
     */
    public function view(User $user, Address $address)
    {
        //
    }

    /**
     * Determine whether the user can create addresses.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the address.
     *
     * @param  User  $user
     * @param  Address  $address
     * @return mixed
     */
    public function update(User $user, Address $address)
    {
        //
    }

    /**
     * Determine whether the user can delete the address.
     *
     * @param  User  $user
     * @param  Address  $address
     * @return mixed
     */
    public function delete(User $user, Address $address)
    {
        //
    }
}
