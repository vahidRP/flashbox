<?php

namespace App\Models\Pivots;

use App\Models\Base\Pivot;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReceipt extends Pivot
{
    /*=================================================
     ******************* Properties *******************
     =================================================*/

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'product_id',
        'receipt_id'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'product_id' => 'integer',
        'receipt_id' => 'integer'
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }
}
