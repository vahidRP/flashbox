<?php

namespace App\Repositories\Criteria\Eloquent;

use App\Support\Repository\Contracts\CriteriaInterface;

/**
 * Class SortByColumns
 *
 * @package App\Repositories\Criteria\Eloquent\.
 */
class SortByColumns implements CriteriaInterface
{
    /**
     * @var array
     */
    private $columns;

    /**
     * SortByColumns constructor.
     *
     * @param array $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param  mixed $query
     *
     * @return mixed
     */
    public function apply($query)
    {
        foreach($this->columns as $field => $direction){
            $explode = explode('.',$field);
            if($explode[0] === 'pivot'){
                $relationName = $explode[1];
                $pivotColumnName = $explode[2];

                $relation = $query->getModel()->{$relationName}();
                $field = $relation->getTable() . ".{$pivotColumnName}";
            }
            $query = $query->orderBy($field, $direction);
        }
        if(empty($this->columns)){
            $model = $query->getModel();
            $field = $model->getConnectionName() === 'crate' ? 'created_at' : $model->getKeyName();
            $query = $query->orderBy($field, 'ASC');
        }
        return $query;
    }
}
