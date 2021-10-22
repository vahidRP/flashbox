<?php

namespace App\Models;

use App\Models\Base\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    /*=================================================
     ********************* Traits *********************
     =================================================*/

    use SoftDeletes;

    /*=================================================
     ******************* Properties *******************
     =================================================*/

    public const USERABLE_KEY = 'userable';
    public const USERABLE_TYPES = [User::class, Store::class];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        (self::USERABLE_KEY . '_type'),
        (self::USERABLE_KEY . '_id'),
        'address',
        'lng',
        'lat'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'lng' => 'decimal:10',
        'lat' => 'decimal:10'
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    /**
     * If it relates to User, So it is user's home address, Otherwise it is stores addresses of sellers
     *
     * @return MorphTo
     */
    public function userable(): MorphTo
    {
        return $this->morphTo(static::USERABLE_KEY);
    }
}
