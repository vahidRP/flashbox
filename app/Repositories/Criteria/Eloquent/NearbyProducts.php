<?php

namespace App\Repositories\Criteria\Eloquent;

use App\Support\Repository\Contracts\CriteriaInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class NearbyProducts
 *
 * @package App\Repositories\Criteria\Eloquent
 */
class NearbyProducts implements CriteriaInterface
{
    /**
     * This unit is KM
     * @var int
     */
    private int $radius = 10;

    public function __construct(int $radius = 10)
    {
        $this->radius = $radius;
    }

    /**
     * @param mixed $query
     * @return mixed
     */
    public function apply($query)
    {
        $user = auth()->user();
        if(count($user?->roles) === 1 && $user->roles->where('identity', 'customer')->first()){
            if($user->address){
                $address = $user->address;
                $latitude = $address->lat;
                $longitude = $address->lng;
                // must just show nearby products
                return $query->whereHas('store', fn($store) => $store->whereHas('address', fn($addr) => $addr->where(DB::raw("(((acos(sin((" . $latitude . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $latitude . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $longitude . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344)"), '>=', $this->radius)));
            }
            return $query->where($query->getModel()->getTable() . '.id', -1);
        }
        return $query;
    }
}
