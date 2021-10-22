<?php

namespace App\Policies;

use App\Models\Store;

class StorePolicy extends Policy
{
    protected string $model = Store::class;
}
