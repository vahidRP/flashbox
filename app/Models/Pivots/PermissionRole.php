<?php

namespace App\Models\Pivots;

use App\Models\Base\Pivot;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRole extends Pivot
{
    /*=================================================
     ******************* Properties *******************
     =================================================*/

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'permission_id',
        'role_id'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'permission_id' => 'integer',
        'role_id' => 'integer'
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
