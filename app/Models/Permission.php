<?php

namespace App\Models;

use App\Models\Base\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
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
