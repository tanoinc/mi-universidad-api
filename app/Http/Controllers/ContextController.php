<?php

namespace App\Http\Controllers;

use App\Context;
use App\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class ContextController extends Controller
{
    
    public function getByApplication(Request $request, $application_name)
    {
        $app = Application::findByName($application_name)->firstOrFail();
        $contexts = Context::searchInApplication($app, $this->getSearchValue());
        return response()->json($contexts->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20)));
    }
}
