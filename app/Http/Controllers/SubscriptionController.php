<?php

namespace App\Http\Controllers;

use App\Context;
use App\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected function getContext($application_name, $context_name, &$app = null)
    {
        $app = Application::findByName($application_name)->firstOrFail();
        return Context::findByName($app, $context_name)->firstOrFail();
    }

    public function subscribeUser(Request $request)
    {
        $user = Auth::user();
        $context = $this->getContext( $request->input('application_name'), $request->input('context_name') );
        $context->subscribe($user);
        
        return response()->json($context);
    }

    public function unsubscribeUser(Request $request, $application_name, $context_name)
    {
        $user = Auth::user();
        $context = $this->getContext($application_name, $context_name);
        $context->unsubscribe($user);
        
        return response()->json($context);
    }

}
