<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;

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

}
