<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomValidationException;
use Illuminate\Validation\ValidationException;
use App\UserApplication;

class Controller extends BaseController
{
    protected $application = null;
    private $request = null;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->setApplication($this->request->attributes->get('application'));
    }

    protected function getApplication()
    {
        return $this->application;
    }
    
    private function setApplication($application)
    {
        $this->application = $application;
    }
    
    protected function getFromUser(User $user)
    {
        throw new \Exception('Method "getFromUser" not implemented in subclass (must be overridden).');
    }
    
    public function getFromUserHashId(Request $request, $user_hash_id)
    {
        $user = User::findByHashId($user_hash_id)->firstOrFail();
        
        return $this->getFromUser($user);
    }

    public function getFromAuthenticatedUser(Request $request)
    {
        return $this->getFromUser(Auth::user());
    }
    
    public function validate(Request $request, array $rules, array $messages = array(), array $customAttributes = array())
    {
        try {
            parent::validate($request, $rules, $messages, $customAttributes);
        }
        catch (ValidationException $e) {
            throw new CustomValidationException($e);
        }
    }
    
    protected function getSearchValue() 
    {
        $search_value = null;
        if ($this->request->has('search')) {
            $search_value = $this->request->get('search');
        }
        return $search_value; 
    }
    
    protected function getUsersFromRequest(Request $request, $with = [])
    {
        $app_id = $this->getApplication()->id;
        if ($request->input('users')) {
            
            return UserApplication::with($with)->findByApplicationAndExternalId( $app_id, $request->input('users') )->get();
        }
        return new \Illuminate\Support\Collection([]);
    }
    
    protected function hydratePage($paginatedCollection, $class)
    {
        $paginatedCollection->setCollection($class::hydrate($paginatedCollection->getCollection()->toArray()));
    }
}
