<?php

namespace App\Models;

use App\Models\Base\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Lumen\Auth\Authorizable;
use App\Models\Pivots\RoleUser;
use App\Models\Traits\Authorization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    /*=================================================
     ********************* Traits *********************
     =================================================*/

    use Authenticatable, Authorizable, SoftDeletes, Authorization;

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

    /**
     * {@inheritdoc}
     */
    protected $with = [
        'roles.permissions'
    ];

    /*=================================================
     **************** JWT Auth Methods ****************
     =================================================*/

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, Address::USERABLE_KEY);
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
