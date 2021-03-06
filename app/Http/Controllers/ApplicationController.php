<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use \App\Application;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

/**
 * Description of ApplicationController
 *
 * @author tanoinc
 */
class ApplicationController extends Controller
{

    public function index(Request $request)
    {
        return response()->json($this->getApplication());
    }

    public function getById($id)
    {
        $application = Application::findOrFail($id);

        return response()->json($application);
    }

    public function getApplicationGrantedPrivileges($id)
    {
        $application = Application::findOrFail($id);

        return response()->json($application->granted_privileges);
    }

    public function getGrantedPrivileges()
    {
        return $this->getApplicationGrantedPrivileges($this->getApplication()->id);
    }

    protected function getValidationRules()
    {
        return [
            'name' => 'required|alpha_dash|unique:application',
            'description' => 'required|max:255',
            'auth_callback_url' => 'url',
            'auth_required' => 'boolean'
        ];
    }

    public function newFromRequest(\Illuminate\Http\Request $request)
    {
        $application = Application::create($request->all());

        return $application;
    }

    public function setFromRequest(\Illuminate\Http\Request $request, Application $application)
    {
        $application->setFromArray($request->all());
    }

    public function createApplication(Request $request)
    {
        $this->validate( $request, $this->getValidationRules() );
        $application = $this->newFromRequest($request);
        $application->save();

        return response()->json($application);
    }

    public function deleteApplication($id)
    {
        $application = Application::findOrFail($id);

        return response()->json( $application->delete() );
    }

    public function updateApplication(Request $request, $id)
    {
        $this->validate( $request, $this->getValidationRules() );
        $application = Application::findOrFail($id);
        $this->setFromRequest($request);
        $application->save();

        return response()->json($application);
    }

    protected function getFromUser(User $user)
    {
        $search_value = $this->getSearchValue();
        
        $applications = $user->subscribed_applications()
                ->search($search_value)
                ->exceptApp(env('MOBILE_APP_NAME')) // Cant unsubscribe from main application
                ->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT', 20));

        return response()->json($applications);
    }

    public function getAvailable(Request $request)
    {
        $search_value = $this->getSearchValue();
        $applications = Application::search($search_value)->notSubscribedBy(Auth::user())
                ->paginate(env('ITEMS_PER_PAGE_DEFAULT', 20));

        return response()->json($applications);
    }

    public function subscribe(Request $request)
    {
        $app = Application::findByName($request->input('application_name'))->firstOrFail();
        $application_subscription = \App\UserApplication::firstOrNew(['application_id' => $app->id, 'user_id' => Auth::user()->id]);
        $application_subscription->application_id = $app->id;
        $application_subscription->user_id = Auth::user()->id;
        if ($app->auth_callback_url != '' and $app->auth_required) {
            $token = $application_subscription->generateSubscriptionToken();
            $application_subscription->save();
            return response()->json(['url_redirect' => $app->auth_callback_url . '?id=' . Auth::user()->hash_id . '&token=' . $token]);
        }
        else {
            $application_subscription->grant($app);
            $application_subscription->save();
            return response()->json($app);
        }
    }

    public function unsubscribe($application_name)
    {
        // Cant unsubscribe from main application
        if (env('MOBILE_APP_NAME') == $application_name) {
            throw new \App\Exceptions\RejectedUnsubscribeApplicationException();
        }
        
        $app = Application::findByName($application_name)->firstOrFail();
        $application_subscription = \App\UserApplication::findByApplicationAndUser($app, Auth::user())->firstOrFail();
        $application_subscription->delete();

        return response()->json($application_subscription);
    }

    public function updateSubscription(Request $request, $id, $token)
    {
        $user = User::findByHashId($id)->firstOrFail();
        $application_subscription = \App\UserApplication::findForSubscription($this->getApplication(), $user, $token)->firstOrFail();
        $application_subscription->external_id = $request->input('external_id');
        $application_subscription->grant($this->getApplication());
        $application_subscription->save();

        return response()->json($application_subscription);
    }

}
