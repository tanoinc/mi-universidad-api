<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

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
    
}
