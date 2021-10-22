<?php

namespace App\Models\Pivots;

use App\Models\Base\Pivot;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleUser extends Pivot
{
    /*=================================================
     ******************* Properties *******************
     =================================================*/

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'role_id',
        'user_id'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'role_id' => 'integer',
        'user_id' => 'integer'
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
