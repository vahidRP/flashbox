<?php

namespace App\Models\Base;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Relations\MorphPivot as BaseMorphPivot;

class MorphPivot extends BaseMorphPivot
{
    use ModelTrait;

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;
}
