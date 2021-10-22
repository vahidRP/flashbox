<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends Policy
{
    protected string $model = User::class;
}
