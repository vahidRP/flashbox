<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Models\Pivots\RoleUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    /*=================================================
     ********************* Traits *********************
     =================================================*/

    use SoftDeletes;

    /*=================================================
     ******************* Properties *******************
     =================================================*/

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [

    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, Address::USERABLE_KEY);
    }

    /**
     * Products which belongs to seller
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->using(RoleUser::class)
            ->withTimestamps()
            ->withPivot((new RoleUser())->getFillable());
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

}
