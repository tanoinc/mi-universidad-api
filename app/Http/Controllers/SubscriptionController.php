<?php

namespace App\Http\Controllers;

use App\Context;
use App\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class SubscriptionController extends Controller
{
    protected function getContext($application_name, $context_name, &$app = null)
    {
        \Illuminate\Support\Facades\Log::debug(sprintf('Retrieving context context: [%s], [%s]', $application_name, $context_name));
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
    
    protected function getFromUser(User $user)
    {
        return response()->json($user->contexts()->with('application')->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20)));
    }
    
    public function getByAppNameFromAuthenticatedUser(Request $request, $application_name)
    {
        $contexts = Context::findByAppAndUser($application_name, Auth::user()); //
        return response()->json($contexts->get());
    }    
}
