<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Models\Pivots\ProductReceipt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
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
        'store_id',
        'title',
        'price'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'store_id' => 'integer',
        'title' => 'string',
        'price' => 'decimal:15',
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function receipts(): BelongsToMany
    {
        return $this->belongsToMany(Receipt::class)
            ->using(ProductReceipt::class)
            ->withTimestamps()
            ->withPivot((new ProductReceipt())->getFillable());
    }

}
