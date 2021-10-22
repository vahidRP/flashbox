<?php

namespace App\Models;

use App\Models\Base\Model;
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
    public const STATUES = ['ordering', 'closed'];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'user_id'      => 'integer',
        'total_amount' => 'decimal:15',
    ];

    /*=================================================
     **************** Relation Methods ****************
     =================================================*/

}
