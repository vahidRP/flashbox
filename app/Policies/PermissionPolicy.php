<?php

namespace App\Policies;

use App\Models\Permission;

class PermissionPolicy extends Policy
{
    protected string $model = Permission::class;
}
