<?php

namespace App\Repositories\Criteria\Eloquent;

use App\Support\Repository\Contracts\CriteriaInterface;
use Illuminate\Support\Facades\Auth;
use RatkoR\Crate\Eloquent\Builder;

/**
 * Class SearchByColumns
 *
 * @package App\Repositories\Criteria\Eloquent\.
 */
class SearchByColumns implements CriteriaInterface
{
    /**
     * @var array
     */
    private $params;

    /**
     * SearchByColumns constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param mixed $query
     *
     * @return mixed
     */
    public function apply($query)
    {
        if (isset($this->params['q']) && !empty($this->params['q']) && isset($this->params['on']) && count($this->params['on'])) {

            return $query->where(function ($q) use ($query) {
                $key = $this->params['q'];

                if ($query->getModel()->getConnectionName() !== 'crate') {
                    $key = !str_contains($key, '%') ? "%{$key}%" : $key;
                }

                $on = ($this->params['on'] ?? []);
                if (Auth::user()->hasPermission('super-admin')) {
                    $on[] = $query->getModel()->getKeyName();
                }
                foreach ($on as $k => $field) {
                    if ($k === 0) {
                        $q = $q->where($field, 'LIKE', $key);
                    } else {
                        $q = $q->orWhere($field, 'LIKE', $key);
                    }
                }
            });
        }
        return $query;
    }
}
