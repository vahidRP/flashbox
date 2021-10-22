<?php

namespace App\Models;

use App\Models\Base\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
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
        'user_id',
        'title',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [

    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, Address::USERABLE_KEY);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
