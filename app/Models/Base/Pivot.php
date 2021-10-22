<?php

namespace App\Models\Base;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Relations\Pivot as BasePivot;

class Pivot extends BasePivot
{
    use ModelTrait;

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;
}
