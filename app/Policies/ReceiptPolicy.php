<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceiptPolicy
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
     * Determine whether the user can view any receipts.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the receipt.
     *
     * @param  User  $user
     * @param  Receipt  $receipt
     * @return mixed
     */
    public function view(User $user, Receipt $receipt)
    {
        //
    }

    /**
     * Determine whether the user can create receipts.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the receipt.
     *
     * @param  User  $user
     * @param  Receipt  $receipt
     * @return mixed
     */
    public function update(User $user, Receipt $receipt)
    {
        //
    }

    /**
     * Determine whether the user can delete the receipt.
     *
     * @param  User  $user
     * @param  Receipt  $receipt
     * @return mixed
     */
    public function delete(User $user, Receipt $receipt)
    {
        //
    }
}
