<?php

namespace App\Repositories\Criteria\Eloquent;

use App\Support\Repository\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class FilterByRelations
 *
 * @package App\Repositories\Criteria\Eloquent\.
 */
class FilterByRelations implements CriteriaInterface
{
    /**
     * @var array
     */
    private $filters;

    /**
     * FilterByRelations constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param mixed $query
     * @return mixed
     */
    public function apply($query)
    {
        foreach($this->filters as $relation => $data){
            $modelType = $data[0];
            unset($data[0]);

            $data = array_reduce($data, function($carry, $item){
                $carry = is_null($carry) ? [] : $carry;
                if(is_array($item)){
                    $carry = array_merge($carry, $item);
                }else{
                    $carry[] = $item;
                }
                return $carry;
            });

            $ids = array_values($data);

            $relations = explode('.', $relation);

            $model = $query->getModel();
            if($model->getConnectionName() === 'crate'){
                $this->handleCrateModel($query, $relations, compact('modelType', 'ids'));
            }else{
                $this->recursiveRelation($query, $relations, compact('modelType', 'ids'));
            }
        }

        return $query;
    }

    protected function handleCrateModel($query, $relations, $extraData)
    {
        if(count($relations)){
            $model = $query->getModel();
            $relation = array_shift($relations);
            $not = str_contains($relation, '!');
            $relation = str_replace('!', '', $relation);

            /**
             * @var BelongsTo $relatedModel
             */
            $relatedModel = $model->{$relation}();

            $q = $relatedModel->getModel()->newQuery();
            $this->recursiveRelation($q, $relations, $extraData);
            $relatedIds = $q->pluck('id')->toArray();

            $query->{$not ? 'whereNotIn' : 'whereIn'}($relatedModel->getForeignKeyName(), $relatedIds);
        }
    }

    protected function recursiveRelation($query, $relations, $extraData)
    {
        if(count($relations)){
            $relation = array_shift($relations);
            $not = str_contains($relation, '!');
            $relation = str_replace('!', '', $relation);

            $relationModel = $query->getModel()->$relation();

            if($relationModel instanceof MorphTo){
                $relationToUpper = strtoupper($relation);

                $morphTypes = get_class($query->getModel()) . "::PERMITTED_{$relationToUpper}_TYPES";
                $modelType = count($relations) === 0 ? "App\\Models\\{$extraData['modelType']}" : get_class($relationModel->getModel());

                $query->where(function($where) use ($not, $relation, $morphTypes, $relations, $extraData, $modelType){
                    $where->{$not ? 'whereDoesntHaveMorph' : 'whereHasMorph'}($relation, constant($morphTypes), function(Builder $q, $type) use ($relations, $extraData, $modelType){
                        if($type === $modelType){
                            $this->recursiveRelation($q, $relations, $extraData);
                        }else{
                            $q->where($q->getModel()->getTable() . '.id', -1);
                        }
                    })->when($not, fn($when) => $when->orWhereNull("{$relation}_type"));
                });
            }else{
                $query->{$not ? 'whereDoesntHave' : 'whereHas'}($relation, function(Builder $q) use ($relations, $extraData){
                    $this->recursiveRelation($q, $relations, $extraData);
                });
            }
        }else{
            $model = $query->getModel();
            $query->whereIn("{$model->getTable()}.{$model->getKeyName()}", $extraData['ids']);
        }
    }
}
