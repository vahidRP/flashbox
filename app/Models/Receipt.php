<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Models\Pivots\ProductReceipt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    /*=================================================
     ********************* Traits *********************
     =================================================*/

    use SoftDeletes;

    /*=================================================
     ******************* Properties *******************
     =================================================*/

    /**
     * Different Statuses of the receipt
     */
    public const STATUES = ['ordering', 'paying', 'closed'];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'total_price',
        'status'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'user_id'      => 'integer',
        'total_price' => 'decimal:15',
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(ProductReceipt::class)
            ->withTimestamps()
            ->withPivot((new ProductReceipt())->getFillable());
    }

}
