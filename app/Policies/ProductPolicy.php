<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
     * Determine whether the user can view any products.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return mixed
     */
    public function view(User $user, Product $product)
    {
        //
    }

    /**
     * Determine whether the user can create products.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return mixed
     */
    public function update(User $user, Product $product)
    {
        //
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return mixed
     */
    public function delete(User $user, Product $product)
    {
        //
    }
}
