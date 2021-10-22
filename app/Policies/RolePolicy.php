<?php

namespace App\Policies;

use App\Models\Role;

class RolePolicy extends Policy
{
    protected string $model = Role::class;
}
