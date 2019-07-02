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
        return $this->getQueryFromUser(Auth::user(), $fn_filter)
                        ->orderBy($order_by_date_field, 'asc');
    }

    protected function getByFutureDate($start_date_field, $fn_custom_filter = null)
    {
        $now = static::now();

        if (!$fn_custom_filter) {
            $fn_custom_filter = function ($query) {
                return $query;
            };
        }        
        
        $fn_filter = function ($query) use ($start_date_field, $now, $fn_custom_filter) {
            return $fn_custom_filter($query)
                ->where($start_date_field, '>=', $now);
        };

        return $this->getByDate($start_date_field, $fn_filter);
    }

    protected function getByNowDate($start_date_field, $end_date_field, $fn_custom_filter = null)
    {
        $now = static::now();
        
        if (!$fn_custom_filter) {
            $fn_custom_filter = function ($query) {
                return $query;
            };
        }

        $fn_filter = function ($query) use ($start_date_field, $end_date_field, $now, $fn_custom_filter) {
            return $fn_custom_filter($query)
                ->where($start_date_field, '<=', $now)
                ->where($end_date_field, '>=', $now);
        };

        return $this->getByDate($start_date_field, $fn_filter);
    }

    protected function getDateableResponse($dateable_information)
    {
        $paginated_dates = $dateable_information
                ->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT', 20));

        $this->hydrateInformation($paginated_dates);

        return response()->json($paginated_dates);
    }

    protected function getResponseByFutureDate($start_date_field, $fn_custom_filter = null)
    {
        $dateable_information = $this->getByFutureDate($start_date_field, $fn_custom_filter);

        return $this->getDateableResponse($dateable_information);
    }

    protected function getResponseByNowDate($start_date_field, $end_date_field, $fn_custom_filter = null)
    {
        $dateable_information = $this->getByNowDate($start_date_field, $end_date_field, $fn_custom_filter);

        return $this->getDateableResponse($dateable_information);
    }

}
