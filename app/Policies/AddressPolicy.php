<?php

namespace App\Policies;

use App\Models\Address;

class AddressPolicy extends Policy
{
    protected string $model = Address::class;
}
