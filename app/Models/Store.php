<?php

namespace App\Models;

use App\Models\Base\Model;
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

}
