<?php

namespace App\Models\Traits;

/**
 * Trait ModelTrait
 *
 * @package App\Models
 */
trait ModelTrait
{

    // ---------------- Helper Methods ----------------

    /**
     * @return string
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @return string
     */
    public static function getPrimaryKeyName()
    {
        return with(new static)->getKeyName();
    }

}
