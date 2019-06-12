<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Description of DateableControllerTrait
 *
 * @author lucianoc
 */
trait InformationDateableControllerTrait
{
    protected static function now()
    {
        return \Carbon\Carbon::now()->toDateTimeString();
    }
    
    protected function getByDate($order_by_date_field, $fn_filter)
    {
        $dateable_information = $this->getQueryFromUser(Auth::user(), $fn_filter)
            ->orderBy($order_by_date_field, 'asc')
            ->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT', 20));
        
        $this->hydrateInformation($dateable_information);
        
        return response()->json($dateable_information);
    }
    
    protected function getFutureByDate($start_date_field)
    {
        $now = static::now();
        
        $fn_filter = function ($query) use ($start_date_field, $now) {
            return $query->where($start_date_field, '>=', $now);
        };
        
        return $this->getByDate($start_date_field, $fn_filter);
    }
    
    protected function getNowByDate($start_date_field, $end_date_field)
    {
        $now = static::now();
        
        $fn_filter = function ($query) use ($start_date_field, $end_date_field, $now) {
            return $query
                ->where($start_date_field, '<=', $now)
                ->where($end_date_field, '>=', $now);
        };
        
        return $this->getByDate($start_date_field, $fn_filter);
    }    
}
