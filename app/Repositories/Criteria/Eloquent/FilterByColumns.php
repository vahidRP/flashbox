<?php

namespace App\Repositories\Criteria\Eloquent;

use App\Support\Repository\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FilterByColumns
 *
 * @package App\Repositories\Criteria\Eloquent\.
 */
class FilterByColumns implements CriteriaInterface
{
    /**
     * @var array
     */
    private $filters;

    /**
     * FilterByColumns constructor.
     *
     * @param $filters
     */
    public function __construct($filters)
    {
        $this->filters = empty($filters) ? [] : $filters;
    }

    /**
     * @param  Builder $query
     *
     * @return mixed
     */
    public function apply($query)
    {
        $model = $query->getModel();
        $casts = $model->getCasts();
        $tableName = $model->getTable();
        $connection = $model->getConnectionName();

        foreach($this->filters as $filter){
            $count = count($filter);
            $field = $filter[0];

            if($connection === 'crate'){
                $fieldExplode = explode('->', $field);
                foreach($fieldExplode as $k => $val){
                    if($k === 0){
                        $field = $val;
                    }else{
                        $field .= "['{$val}']";
                    }
                }
            }

            $operand = $count === 2 ? '=' : $filter[1];
            $value = $count === 2 ? $filter[1] : $filter[2];

            if($value !== '*'){

                if(is_array($value)){
                    $query = $query->where(function($q) use ($field, $operand, $value, $tableName){

                        foreach($value as $k => $val){
                            $operand2 = $operand;
                            if(strpos($val, '!') === 0 && $operand === '='){
                                $val = str_replace('!', '', $val);
                                $operand2 = '!=';
                            }
                            if($k === 0){
                                $q->where("{$tableName}.{$field}", $operand2, $val);
                            }else{
                                $q->orWhere("{$tableName}.{$field}", $operand2, $val);
                            }
                        }
                    });
                }else{
                    if(strpos($value, '!') === 0 && $operand === '='){
                        $value = str_replace('!', '', $value);
                        $operand = '!=';
                    }

                    if(isset($casts[$field]) && $casts[$field] === 'boolean'){
                        $value = $value == 'true' || $value == '1' ? 1 : 0;
                    }
                    $value = $value === '' ? null : $value;
                    $query = $query->where("{$tableName}.{$field}", $operand, $value);
                }
            }
        }

        return $query;
    }
}
